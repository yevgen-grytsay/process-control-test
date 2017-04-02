## Publish/Subscribe

Start subscriber
```bash
php shmem_subscriber.php "shmem.php.bak"
```

Write "a"
```bash
php shmem_publisher.php "shmem.php.bak" "a"
```

Terminate subscriber
```bash
php shmem_publisher.php "shmem.php.bak" "="
```

Example 2
```bash
$ php shmem_subscriber2.php
Shared memory key: 1627853115

$ php shmem_publisher2.php 1627853115 "="
```
