<?php

declare(strict_types=1);

namespace KSamuel\RrService\Connection;

use Foolz\SphinxQL\Drivers\Mysqli\Connection;
use Foolz\SphinxQL\SphinxQL;

/**
 * @package App\Connection
 */
class Sphinx implements ConnectionInterface
{
    /**
     * @var array<string,mixed>
     */
    protected array $config = [];

    protected ?Connection $connection = null;

    /**
     * @param array<string,mixed> $config
     */
    public function __construct(array $config)
    {
        foreach ($config as $k => $v) {
            $this->config[$k] = $v;
        }
    }

    /**
     * Compatibility hack
     * @return void
     * @throws \RuntimeException
     */
    public function connect(): void
    {
        if (empty($this->connection)) {
            $this->connection = new Connection();
            $this->connection->setParams(
                [
                    'host' => $this->config['host'],
                    'port' => $this->config['port']
                ]
            );
            if (!$this->connection->connect()) {
                throw new \RuntimeException('Cannot connect sphinx service');
            }
        }
    }

    public function getConnection(): Connection
    {
        if (empty($this->connection)) {
            $this->connect();
        }
        /**
         * @var Connection
         */
        return $this->connection;
    }

    public function createQuery(): SphinxQL
    {
        return new SphinxQL($this->getConnection());
    }

    public function splitQuery(string $query): string
    {
        $aKeyword = [];
        $aRequestString = preg_split('/[\s,-]+/', $query, 5);
        $sSphinxKeyword = '';
        if ($aRequestString) {
            foreach ($aRequestString as $sValue) {
                if (strlen($sValue) > 2) {
                    $aKeyword[] = "(" . $sValue . " | *" . $sValue . "*)";
                }
            }
            $sSphinxKeyword = implode(" | ", $aKeyword);
        }
        return $sSphinxKeyword;
    }

    /**
     * Очистить строку запроса для полнотекстного поиска от лишних символов
     * @param string $string
     * @return string
     */
    public function clearQueryString(string $string): string
    {
        $string = (string)preg_replace("/[^A-Za-zА-Яа-я0-9_\.\-\, ]/ui", '', $string);
        $string = (string)preg_replace("/(\.|\,){2,}/", '', $string);
        $string = (string)preg_replace("/\s{2,}/", " ", $string);
        return $string;
    }

    /**
     *  Извлечь список слов из поисковой строки
     * @param string $query
     * @return string[]
     */
    public function extractWords(string $query): array
    {
        if (mb_strlen($query, 'utf8') < 3) {
            return [];
        }
        $cleanedString = $this->clearQueryString($query);
        $words = explode(' ', $cleanedString);
        foreach ($words as $index => &$item) {
            // не принимаем слова длиной более 25
            if (mb_strlen($item, 'utf-8') > 25) {
                $item = mb_substr($item, 0, 25, 'utf-8');
            }
            if (empty($item)) {
                unset($words[$index]);
            }
        }
        unset($item);
        return $words;
    }

    public function close()
    {
        if ($this->connection instanceof Connection) {
            try {
                $this->connection->close();
            } catch (\Throwable $e) {
                // nothing to do connection is not opened
            }
        }
        $this->connection = null;
    }

    public function getProfiler()
    {
        return null;
    }
}
