#!/bin/bash
mysqldump -uroot -p refereeplanng > `date +%d-%m-%Y`.sql
