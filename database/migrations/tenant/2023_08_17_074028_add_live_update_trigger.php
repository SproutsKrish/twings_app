<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER `live_data_update` AFTER INSERT ON `new_location_history`
 FOR EACH ROW BEGIN
DECLARE current_odometer DECIMAL(10,2) DEFAULT 0; /* Declare Current Odometer Variable */
/* Start Inserting deviceimei Current Details Get From Livedata Table */
 SELECT deviceimei,client_id,vehicle_id,vehicle_status,odometer,ignition_report_flag,ignition_report_datetime
            INTO @deviceimei,@client_id,@vehicle_id,@vehicle_status,@odometer,@ignition_report_flag,@ignition_report_datetime
            FROM live_data
            WHERE
            deviceimei = NEW.deviceimei ORDER BY  vehicle_id DESC LIMIT 1;
           /* START - CALCULATION OF ODOMETER */
           IF @deviceimei IS NOT NULL AND @vehicle_status = 1 AND NEW.io_state!=1 AND NEW.packet_status = 0 THEN
           SET current_odometer = ROUND(NEW.distance_with_odometer,2) ;
           ELSEIF NEW.lattitute!= 0.0 THEN
           SET current_odometer = ROUND(@odometer+NEW.distance_without_odometer,2);
           END IF;
           /* END - CALCULATION OF ODOMETER */
           /* START - LIVE DATA UPDATION */

 UPDATE live_data
 SET
 ignition = NEW.ignition,
                    device_updatedtime = NEW.device_datetime,
                    lattitute = NEW.lattitute,
                    longitute = NEW.longitute,
                    speed = NEW.speed,
                    angle = NEW.angle,
                    altitude=NEW.altitude,
                    gpssignal=NEW.gpssignal,
                    gsm_status=NEW.gsm_status,
                    vehicle_sleep=NEW.vehicle_sleep,
                    odometer=current_odometer
 WHERE deviceimei = @deviceimei;
           /* END - LIVE DATA UPDATION */
 IF((@ignition_report_flag = 1 OR @ignition_report_flag = 0 OR @ignition_report_flag IS NULL) AND(NEW.ignition = 1) ) THEN
  /* LIVE DATA UPDATING */
UPDATE live_data
SET
ignition_report_flag = 2,
last_ignition_on_time = NEW.device_datetime,
last_ignition_off_time = NEW.device_datetime,
ignition_report_datetime = NEW.device_datetime
WHERE
deviceimei = NEW.deviceimei;
/* LIVE DATA UPDATING */
/*Start Insert Trip report */
INSERT INTO keyoff_keyon_reports(
                                flag,
                                start_latitude,
                                start_longitude,
                                start_datetime,
                                device_imei,
                                vehicle_id,
                                start_odometer,
                                client_id
                            )
                            VALUES(
                            1,
                            NEW.lattitute,
                            NEW.longitute,
                            NEW.device_datetime,
                            NEW.deviceimei,
                            @vehicle_id,
                            current_odometer,
                            @client_id
                            );
/*End Insert Trip report */
/*Start Update parking Report*/

                            UPDATE
                            parking_reports
                            SET
                            flag = 2,
                            end_datetime = NEW.device_datetime,
                            end_latitude = NEW.lattitute,
                            end_longitude = NEW.longitute
                            WHERE
                            device_imei = NEW.deviceimei AND flag = 1;
                            END IF;
 /*End Update parking Report*/
 /* START - INSERT IDLE REPORT*/
 IF(NEW.speed = 0 AND @ignition_report_flag = 2 AND NEW.ignition = 1 ) THEN
 /* UPDATE LIVE LOCATION DATA */
 UPDATE
                                live_data
                                SET
                                vehicle_current_status=2,
                                ignition_report_flag = 3,
                                last_ignition_on_time = NEW.device_datetime,
                                last_ignition_off_time = NEW.device_datetime,
                                ignition_report_datetime = NEW.device_datetime
                                WHERE
                                deviceimei = NEW.deviceimei;
 /* UPDATE LIVE LOCATION DATA */
  INSERT
                                INTO
                                idle_reports(
                                    flag,
                                    start_latitude,
                                    start_longitude,
                                    start_datetime,
                                    device_imei,
                                    vehicle_id,
                                    client_id
                                )
                                VALUES(
                                1,
                                NEW.lattitute,
                                NEW.longitute,
                                NEW.device_datetime,
                                NEW.deviceimei,
                                @vehicle_id,
                                @client_id
                                );
 END IF;
 /* START - PARKING REPORT*/
 IF(@ignition_report_flag = 2 OR @ignition_report_flag = 3) AND NEW.ignition = 0 THEN
 UPDATE
                            live_data
                            SET
                            vehicle_current_status=1,
                            ignition_report_flag = 0,
                            last_ignition_on_time = NEW.device_datetime,
                            last_ignition_off_time = NEW.device_datetime
                            WHERE
                            deviceimei = NEW.deviceimei;
                            UPDATE
                            keyoff_keyon_reports
                            SET
                            flag = 2,
                            end_datetime = NEW.device_datetime,
                            end_latitude = NEW.lattitute,
                            end_longitude = NEW.longitute,
                            end_odometer= current_odometer
                            WHERE
                            device_imei = NEW.deviceimei AND flag = 1;
INSERT
                            INTO
                            parking_reports(
                                flag,
                                start_latitude,
                                start_longitude,
                                start_datetime,
                                device_imei,
                                vehicle_id
                            )
                            VALUES(
                            1,
                            NEW.lattitute,
                            NEW.longitute,
                            NEW.device_datetime,
                            NEW.deviceimei,
                            @vehicle_id
                            );
                            UPDATE
                            idle_reports
                            SET
                            flag = 2,
                            end_datetime = NEW.device_datetime,
                            end_latitude = NEW.lattitute,
                            end_longitude = NEW.longitute
                            WHERE
                            device_imei = NEW.deviceimei AND flag = 1;
 END IF;
 /* END - PARKING REPORT*/
/* START - PLAYBACK HISTORY*/
  IF( @ignition_report_flag = 3 AND NEW.speed > 0 AND NEW.ignition = 1
                            ) THEN
 UPDATE
                            live_data
                            SET
                            vehicle_current_status=3,
                            ignition_report_flag = 2,
                            last_ignition_on_time = NEW.device_datetime,
                            last_ignition_off_time = NEW.device_datetime,
                            ignition_report_datetime = NEW.device_datetime
                            WHERE
                            deviceimei = NEW.deviceimei;
                            UPDATE
                            idle_reports
                            SET
                            flag = 2,
                            end_datetime = NEW.device_datetime,
                            end_latitude = NEW.lattitute,
                            end_longitude = NEW.longitute
                            WHERE
                            device_imei = NEW.deviceimei AND flag = 1;
 END IF;
 IF NEW.ignition = 1 AND NEW.lattitute!=0.0 AND NEW.longitute!=0.0 AND NEW.lattitute!='000000000' AND NEW.longitute!='000000000' AND NEW.speed>1 THEN
INSERT
                INTO
                play_back_histories(
                    device_imei,
                    latitude,
                    longitude,
                    speed,
                    angle,
                    ignition,
                    device_datetime,
                    odometer,
                    client_id
                )
                VALUES(
                NEW.deviceimei,
                NEW.lattitute,
                NEW.longitute,
                NEW.speed,
                NEW.angle,
                NEW.ignition,
                NEW.device_datetime,
                current_odometer,
                @client_id
                );
END IF;
/* END - PLAYBACK HISTORY*/
IF( @ignition_report_flag = 3 AND NEW.speed > 0 AND NEW.ignition = 1
                            ) THEN
 UPDATE
                            live_data
                            SET
                            vehicle_current_status=3,
                            ignition_report_flag = 2,
                            last_ignition_on_time = NEW.device_datetime,
                            last_ignition_off_time = NEW.device_datetime,
                            ignition_report_datetime = NEW.device_datetime
                            WHERE
                            deviceimei = NEW.deviceimei;
                            UPDATE
                            idle_reports
                            SET
                            flag = 2,
                            end_datetime = NEW.device_datetime,
                            end_latitude = NEW.lattitute,
                            end_longitude = NEW.longitute
                            WHERE
                            device_imei = NEW.deviceimei AND flag = 1;
 END IF;
 IF NEW.ignition = 1 AND NEW.lattitute!=0.0 AND NEW.longitute!=0.0 AND NEW.lattitute!='000000000' AND NEW.longitute!='000000000' AND NEW.speed>1 THEN
INSERT
                INTO
                play_back_histories(
                    device_imei,
                    latitude,
                    longitude,
                    speed,
                    angle,
                    ignition,
                    device_datetime,
                    odometer,
                    client_id
                )
                VALUES(
                NEW.deviceimei,
                NEW.lattitute,
                NEW.longitute,
                NEW.speed,
                NEW.angle,
                NEW.ignition,
                NEW.device_datetime,
                current_odometer,
                @client_id
                );
END IF;
/* END - PLAYBACK HISTORY*/
/* END - GENERIC REPORT*/

       END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
