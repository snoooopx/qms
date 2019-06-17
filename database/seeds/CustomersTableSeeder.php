<?php

use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $procedure = '
            CREATE PROCEDURE `insertRandomCustomers`(IN qty int)
            BEGIN
                DECLARE i INT DEFAULT 0;
                PREPARE stmt 
                FROM \'INSERT INTO `customers` (`name`,`email`,`sex`,`country_id`)
                     VALUES(?, ?, ?, ?)\';
            
                WHILE i < qty DO
                    SET @randomSex = FLOOR(rand()*3);
                    SET @randomCountry = FLOOR(rand()*5);
                    SET @v1 = concat(\'test user\', i);
                    SET @v2 = concat(\'test user\', i, \'@qms.io\');
                    SET @v3 = CASE
                        WHEN @randomSex=0 THEN \'m\'
                        WHEN @randomSex=1 THEN \'f\'
                        ELSE \'other\'
                    END;
                    SET @v4 = CASE
                        WHEN @randomCountry=0 OR @randomCountry=1 THEN @randomCountry+1
                        ELSE 13
                    END;
                     
                    EXECUTE stmt USING @v1, @v2, @v3, @v4;
                    SET i = i + 1;
                END WHILE;
                DEALLOCATE PREPARE stmt;
            END
        ';

        DB::unprepared("DROP procedure IF EXISTS insertRandomCustomers");
        DB::unprepared($procedure);
        DB::select('call insertRandomCustomers(30000)');
    }
}
