<?php

    namespace App\Models\feed;

    use Google_Service_Drive;
    use Google_Service_Drive_DriveFile;
    use Illuminate\Contracts\Filesystem\FileNotFoundException;
    use Illuminate\Support\Facades\Storage;

    class GoogleDrive
    {
        private $drive;
        private $service;
        private $houve_erro = false;

        /**
         * GoogleDrive constructor.
         */
        public function __construct()
        {
            try {
                $this->drive = new \Google_Client();
                $this->drive->setApplicationName("My Project");
                $this->drive->setScopes(Google_Service_Drive::DRIVE_METADATA_READONLY);
                $this->drive->setAuthConfig(Storage::path("client_id.json"));
                $this->drive->setAccessType('offline');
                $tokenPath = 'token.json';
                if (Storage::exists($tokenPath)) {
                    $accessToken = json_decode(Storage::get($tokenPath), true);
                    $this->drive->setAccessToken($accessToken);
                }
                if ($this->drive->isAccessTokenExpired()) {
                    if ($this->drive->getRefreshToken()) {
                        $this->drive->fetchAccessTokenWithRefreshToken($this->drive->getRefreshToken());
                    } else {
                        $authUrl = $this->drive->createAuthUrl();
                        $accessToken = $this->drive->fetchAccessTokenWithAuthCode("4/2AEmPy6LNVVV_RQ7-nVXHJl2BSqXRmcTb-esOsM0oAtpbBTiZ7gaiSjfaysTyJxLnMRTKCK25NQ1Tv7JjgN1wqs");
                        $this->drive->setAccessToken($accessToken);

                        // Check to see if there was an error.
                        if (array_key_exists('error', $accessToken)) {
                            \Log::info("drive expirado!!");
                            $this->houve_erro = true;
                        }
                    }
                    Storage::put($tokenPath, json_encode($this->drive->getAccessToken()));
                }
                $this->service = new \Google_Service_Drive($this->drive);
            } catch (\Google_Exception $e) {
                \Log::error($e);
                $this->houve_erro = true;
            } catch (FileNotFoundException $e) {
                \Log::error($e);
                $this->houve_erro = true;
            }
        }

        /**
         * @param $id_path
         * @return bool|Google_Service_Drive_DriveFile
         */
        private function getIDpathContents($id_path)
        {
            if (!$this->houve_erro) {
                $query = "'$id_path' in parents";
                $optParams = [
                    'fields' => 'files(id,name)',
                    'q' => $query
                ];
                $results = $this->service->files->listFiles($optParams);
                return $results->getFiles();
            }
            return false;
        }

        public function getLinksInPath($id_path)
        {
            $result = $this->getIDpathContents($id_path);
            $link_padrao = "https://docs.google.com/document/d/";
            $links = [];
            if ($result != false) {
                foreach ($result as $file) {
                    $links[] = $link_padrao . $file->getID() . "/edit";
                }
            }
            return $links;
        }

        public function getLinksInPathWithTitle($id_path)
        {
            $result = $this->getIDpathContents($id_path);
            $link_padrao = "https://docs.google.com/document/d/";
            $links = [];
            if ($result != false) {
                foreach ($result as $file) {
                    $links[] = ["link" => $link_padrao . $file->getID() . "/edit", "title" => $file->getName()];
                }
            }
            return $links;
        }

    }
