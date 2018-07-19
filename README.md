# vk-music-downloader

## Usage:

Install the php 7.1.3+ from http://php.net/downloads.php

Install GIT from https://git-scm.com/

```
git clone https://github.com/kucheriavij/vk-music-downloader.git
```

Install PHP dependencies:
```
php composer.phar install
```

Configure in .env file you VK data:
```
VK_LOGIN=you_vk_login
VK_PASSWORD=you_vk_password
VK_UID=you_vk_uid
```

Configure database settings in .env file (mysqli by default).

Create database:
```
php bin\console doctrine:database:create
```

Apply Audio entity:
```
php bin\console doctrine:schema:update --force
```

Download audio. Default values for --limit and --offset set in 0, --uid - you vk id.
If you set --offset, download will take a very long time, because VK it bans requests.
```
php bin\console vk:download [int --limit=0] [int --offset=0] [int --uid=111111111]
```

vk:download command options:
```
--limit limit downloaded audios
--offset offset from track download
--uid vk user id from download audios (if audios open in privacy)
```

If you want to see the list of downloaded audio...

Install nodejs from https://nodejs.org/en/

Install node modules:
```
npm install
```

Build assets:
```
npm run dev
```

Run php server:
```
php bin\console server:run
```

Open in you browser: http://127.0.0.1:8000