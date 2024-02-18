# Log Files

Make sure folders are created for creating and writing log files.

```bash
$ sudo mkdir /var/log/plat4m
$ sudo chmod 0777 /var/log/plat4m
$ sudo mkdir /var/log/plat4m/logs
$ sudo mkdir /var/log/plat4m/errors
$ sudo chmod -R 0777 /var/log/plat4m
```

# Setup CRON To Delete Log Files Periodically

```
$ cd /etc/cron.daily
$ sudo nano delete-plat4m-logs
```
Enter the following contents in the editor.
```
#!/bin/bash -e
sudo /usr/bin/find /var/log/plat4m/logs -name "*.log" -type f -mtime +7 -delete
```
Change permissions to run the cron and restart cron service.
```
$ chmod 0755 /etc/cron.daily/delete-plat4m-logs
$ sudo systemctl restart cron.service
```

# Install cURL
Inorder to run cURL from PHP, we need this extension.
```
$ sudo apt-get install php-curl
$ sudo service apache2 restart
```