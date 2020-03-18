<?php

namespace Phoenix\Util\InFileStorage;

use SplFileObject;

class SingleFileDao implements Dao
{
    private $file;
    private $cursor;
    private $compactEach;

    const OFFSET_AUTOINCREMENT = 0;
    const OFFSET_VERSION = 4;
    const OFFSET_LAST_ROW_OFFSET_ADDR = 8;
    const OFFSET_FIRST_ROW_OFFSET = 12;

    public function __construct(string $filePath, int $compactEach = 100)
    {
        if (!file_exists($filePath)) {
            touch($filePath);
        }
        $this->file = new SplFileObject($filePath, 'r+');
        if (0 == $this->file->getSize()) {
            $this->transactional(function () {
                $this->file->fwrite(pack('III', 1, 0, 0));
            });
        }
        $this->compactEach = $compactEach;
    }

    private function transactional(callable $callback)
    {
        while (false == $this->file->flock(LOCK_EX)) {
            usleep(200);
        }

        $result = $callback();

        $this->file->fflush();
        $this->file->flock(LOCK_UN);

        return $result;
    }

    private function rowIterator(): iterable
    {
        $this->cursor = self::OFFSET_FIRST_ROW_OFFSET;
        while ($row = $this->readRight()) {
            if ($row['id'] != 0) {
                yield $row;
            }
        }
    }

    private function rowReverseIterator(): iterable
    {
        $this->file->fseek(self::OFFSET_LAST_ROW_OFFSET_ADDR);
        $this->cursor = unpack('I', $this->file->fread(4))[1];
        while ($row = $this->readLeft()) {
            if ($row['id'] != 0) {
                yield $row;
            }
        }
    }

    private function readLeft()
    {
        if ($this->cursor > 0) {
            $row = $this->readRow($this->cursor);
            if (isset($row)) {
                $this->cursor = $row['prev'];
                return $row;
            }
        }

        return null;
    }

    private function readRight()
    {
        $row = $this->readRow($this->cursor);
        if (isset($row)) {
            $this->cursor = $row['next'];
            return $row;
        }

        return $row;
    }

    private function readRow($pos)
    {
        $this->file->fseek($pos);
        $head = $this->file->fread(12);
        if (empty($head)) {
            return null;
        }
        $row = unpack('Iid/Iprev/Ilen', $head);
        $row['pos'] = $pos;
        $row['pos'] = $pos;
        $row['next'] = $pos + 12 + $row['len'];
        $row['data'] = $row['len'] > 0 ? unserialize($this->file->fread($row['len'])) : null;

        return $row;
    }

    private function appendRow($id, $data)
    {
        $this->file->fseek(self::OFFSET_LAST_ROW_OFFSET_ADDR);
        $lastRowPos = unpack('I', $this->file->fread(4))[1];
        $this->file->fseek(0, SEEK_END);
        $curPos = $this->file->ftell();
        $strData = serialize($data);
        $this->file->fwrite(pack('III', $id, $lastRowPos, strlen($strData)));
        $this->file->fwrite($strData);
        $this->file->fseek(self::OFFSET_LAST_ROW_OFFSET_ADDR);
        $this->file->fwrite(pack('I', $curPos));
    }

    private function compact()
    {
        $tmpFilePath = $this->file->getPath() . '/_tmp_' . $this->file->getBasename();
        $out = new self($tmpFilePath, 0);
        foreach ($this->getIterator() as $id => $data) {
            $out->put($id, $data);
        }
        $this->file->fseek(self::OFFSET_AUTOINCREMENT);
        $autoincrement = unpack('I', $this->file->fread(4))[1];
        $out->file->fseek(self::OFFSET_AUTOINCREMENT);
        $out->file->fwrite(pack('I', $autoincrement));

        $this->file->fseek(self::OFFSET_VERSION);
        $version = unpack('I', $this->file->fread(4))[1];
        $out->file->fseek(self::OFFSET_VERSION);
        $out->file->fwrite(pack('I', $version));

        rename($tmpFilePath, $this->file->getPathname());
    }

    private function incrementVersion(): int
    {
        $this->file->fseek(self::OFFSET_VERSION);
        $version = unpack('I', $this->file->fread(4))[1];

        $version ++;
        $this->file->fseek(self::OFFSET_VERSION);
        $this->file->fwrite(pack('I', $version));

        if ($this->compactEach > 0 && (0 == ($version % $this->compactEach))) {
            $this->compact();
        }

        return $version;
    }

    public function put(int $id, $newData): bool
    {
        return $this->transactional(function () use ($id, $newData) {
            foreach ($this->rowIterator() as $row) {
                if ($id == $row['id']) {
                    if ($row['data'] == $newData) {
                        return false;
                    }
                    $this->file->fseek($row['pos']);
                    $this->file->fwrite(pack('I', 0));
                    break;
                }
            }
            $this->appendRow($id, $newData);
            $this->incrementVersion();
            return true;
        });
    }

    public function remove(int $id): bool
    {
        return $this->transactional(function () use ($id) {
            foreach ($this->rowIterator() as $row) {
                if ($id == $row['id']) {
                    $this->file->fseek($row['pos']);
                    $this->file->fwrite(pack('I', 0));
                    $this->incrementVersion();
                    return true;
                }
            }
            return false;
        });
    }

    public function get(int $id)
    {
        foreach ($this->rowIterator() as $row) {
            if ($row['id'] == $id) {
                return $row['data'];
            }
        }

        return null;
    }

    public function getFromEnd(int $id)
    {
        foreach ($this->rowReverseIterator() as $row) {
            if ($row['id'] == $id) {
                return $row['data'];
            }
        }

        return null;
    }

    public function nextId(): int
    {
        return $this->transactional(function() {
            $this->file->fseek(self::OFFSET_AUTOINCREMENT);
            $autoincrementValue = unpack('I', $this->file->fread(4))[1];

            $this->file->fseek(self::OFFSET_AUTOINCREMENT);
            $this->file->fwrite(pack('I', $autoincrementValue + 1));

            return $autoincrementValue;
        });
    }

    public function getIterator(): iterable
    {
        foreach ($this->rowIterator() as $row) {
            yield $row['id'] => $row['data'];
        }
    }

    public function getReverseIterator(): iterable
    {
        foreach ($this->rowReverseIterator() as $row) {
            yield $row['id'] => $row['data'];
        }
    }
}
