<?php

namespace App\Http\Services;

use App\Models\DataCatalog;

class CatalogService
{
    protected array $data = [];
    protected array $errors = [];
    protected array $headers = [];

    public function storeAndGetCsv($file)
    {
        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$this->headers) {
                    $this->headers = (array) $row;
                    continue;
                }

                if ($this->validate($row)) {
                    $this->data[] = array_combine($this->headers, $row);
                }
            }

            fclose($handle);
        }

        DataCatalog::upsert($this->data, 'code');

        return $this->makeCsv();
    }

    protected function validate(array $row): bool
    {
        preg_match_all('/[^a-zA-ZА-яа-я0-9-.]/u', $row[1], $errors);

        if (!empty($errors[0])) {
            $error = sprintf('Недопустимый символ `%s`', collect($errors)->collapse()->implode(','));
            $this->errors[] = array_merge(array_combine($this->headers, $row), [$error]);

            return false;
        }

        return true;
    }

    protected function makeCsv()
    {
        return $callback = function () {
            $file = fopen('php://output', 'w');

            fputcsv($file, array_merge($this->headers, ['error']), ',');

            foreach ($this->errors as $line) {
                fputcsv($file, $line, ',');
            }

            foreach ($this->data as $line) {
                fputcsv($file, $line, ',');
            }

            fclose($file);
        };
    }
}