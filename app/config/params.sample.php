<?php

return [
    'domain.www.home' => 'http://www.example.com/',
    'domain.img.tpl' => 'http://img%d.example.com:8899/',
    'domain.img.num' => 20,
    'domain.video.tpl' => 'http://video%d.example.com:8899/',
    'domain.video.num' => 10,
    
    'admini.admin.salt' => 'a secret string',
    'admin.smtp.server' => array(
        'host' => 'smtp.qiye.163.com',
        'port' => 25,
        'user' => 'username',
        'passwd' => 'password',
        'from' => ['username@163.com' => 'Username']
    ),
    'admin.warn.mail' => ['username@163.com'],
];
