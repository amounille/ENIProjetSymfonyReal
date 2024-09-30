<?php

namespace App\Service;

class ImportUsersService {

    private string $fileName = '';
    private array $dataUsers = [];

    public const FIELDS_LINE        = 'Email,Password,Name,Firstname,Phonenumber';
    public const CELL_EMAIL         = 'email';
    public const CELL_PASSWORD      = 'password';
    public const CELL_NAME          = 'name';
    public const CELL_FIRSTNAME     = 'firstname';
    public const CELL_PHONENUMBER   = 'phonenumber';

    public function __construct() {}

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getDataUsers(): ?array
    {
        return $this->dataUsers;
    }

    public function addUser(array $user): static
    {
        array_push($this->dataUsers,$user);
        
        return $this;
    }

    public function extractData()
    {
        if (($handle = fopen($this->getFileName(), "r")) !== FALSE) {
            // Get the first line of the file
            $arrayTitles = fgetcsv($handle, 1000, ",");
            // Set the line titles and check it
            $lineTitles = $arrayTitles[0].','.$arrayTitles[1].','.$arrayTitles[2].','.$arrayTitles[3].','.$arrayTitles[4];
            if ($lineTitles === $this::FIELDS_LINE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $dataUser = [
                        $this::CELL_EMAIL => $data[0],
                        $this::CELL_PASSWORD => $data[1],
                        $this::CELL_NAME => $data[2],
                        $this::CELL_FIRSTNAME => $data[3],
                        $this::CELL_PHONENUMBER => $data[4],
                    ];
                    $this->addUser($dataUser);
                }
                fclose($handle);
            }
        }
    }
}