@echo off
echo Importation des données dans la base de données...
mysql -u root -p ladatema < database/seeds/restore_data_fixed.sql
echo Importation terminée!
pause
