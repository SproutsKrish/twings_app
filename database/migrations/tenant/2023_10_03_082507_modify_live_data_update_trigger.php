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
 FOR EACH ROW
 BEGIN
 DECLARE current_odometer DECIMAL(10,2) DEFAULT 0; /* Declare Current Odometer Variable */
 DECLARE current_distance DECIMAL(10,2) DEFAULT 0; /* Declare Current Distance */
 DECLARE current_vehicle_status INTEGER; /* Declare Current Status - No network */
 /* Start Inserting deviceimei Current Details Get From Livedata Table */
  SELECT deviceimei,client_id,vehicle_id,vehicle_current_status,vehicle_status,odometer,ignition_report_flag,ignition_report_datetime,device_battery_volt,vehicle_battery_volt,lattitute,longitute,device_updatedtime,speed,TIMESTAMPDIFF(
     MINUTE,
     ignition_report_datetime,
     NEW.device_datetime
   ),current_alert_status,ac_flag
             INTO @deviceimei,@client_id,@vehicle_id,@vehicle_current_status,@vehicle_status,@odometer,@ignition_report_flag,@ignition_report_datetime,@device_battery_volt,@vehicle_battery_volt,@lattitute,@longitute,@device_updatedtime,@speed,@diff_time,@current_alert_status,@ac_flag
             FROM live_data
             WHERE
             deviceimei = NEW.deviceimei  ORDER BY  vehicle_id DESC LIMIT 1;
 /* End Inserting deviceimei Current Details Get From Livedata Table */
SELECT ignition_flag INTO @current_ignition FROM twings.live_notifications WHERE device_imei=NEW.deviceimei ORDER BY id desc LIMIT 1;
 /* START - CALCULATION OF ODOMETER */
            IF @deviceimei IS NOT NULL AND @vehicle_status = 1 AND (NEW.io_state=0 OR NEW.io_state= 2 OR NEW.io_state=4) AND NEW.packet_status=0 THEN
            SET current_odometer = @odometer;
            IF NEW.distance_with_odometer != '0' AND NEW.distance_with_odometer != '0.0' THEN
                             SET current_odometer = NEW.distance_with_odometer ;
                             ELSEIF NEW.lattitute!= '0.000000' AND NEW.longitute!= '0.000000' AND (NEW.io_state=0 OR NEW.io_state= 2 OR NEW.io_state=4) AND NEW.packet_status=0 AND NEW.ignition=1 AND NEW.speed>1 THEN
                             SET current_distance =  (ST_DISTANCE_SPHERE(POINT(NEW.lattitute, NEW.longitute),POINT(@lattitute, @longitute)) / 1000);
                                  IF current_distance != 0 AND current_distance IS NOT NULL THEN
                                     SET current_odometer = ROUND(@odometer + current_distance, 2);
                                 END IF;
                             END IF;
            END IF;
 /* END - CALCULATION OF ODOMETER */

 /* START - LIVE DATA UPDATION */

 /* IO-STATE 0 OR 4 DATA UPDATION - STARTS HERE*/
 IF NEW.packet_status = 0 AND (NEW.io_state=0 OR NEW.io_state=4) AND NEW.lattitute!= '0.000000' AND NEW.longitute!= '0.000000' THEN
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
 device_battery_volt = NEW.device_battery_volt,
 vehicle_battery_volt = NEW.vehicle_battery_volt,
 power_status = NEW.power_status,
 odometer = current_odometer,
 battery_percentage = NEW.device_battery_percent
  WHERE deviceimei = @deviceimei;
  END IF;
 /* IO-STATE 0 OR 4 DATA UPDATION - ENDS HERE*/

 /* IO-STATE 2 DATA UPDATION - STARTS HERE*/
 IF NEW.packet_status = 0 AND (NEW.io_state=2) AND NEW.lattitute!= '0.000000' AND NEW.longitute!= '0.000000' THEN
  UPDATE live_data
  SET
  ignition = NEW.ignition,
 device_updatedtime = NEW.device_datetime,
 lattitute = NEW.lattitute,
 longitute = NEW.longitute,
 speed = NEW.speed,
 angle = NEW.angle,
 odometer = current_odometer,
 power_status = NEW.power_status
  WHERE deviceimei = @deviceimei;
  END IF;
 /* IO-STATE 2 DATA UPDATION - ENDS HERE */

 /* START - UPDATING DEVICE DATE TIME ON HEART BEAT PACKET*/
 IF NEW.io_state=1 THEN
  UPDATE live_data
  SET
  device_updatedtime = NEW.device_datetime
  WHERE deviceimei = @deviceimei;
  END IF;
  /* END - UPDATING DEVICE DATE TIME ON HEART BEAT PACKET*/
  /* IO_STATE -3 : HEART PACKET WITH BATTERY*/
 IF NEW.io_state=3 THEN
  UPDATE live_data
  SET
  ignition = NEW.ignition,
  speed = NEW.speed,
  device_updatedtime = NEW.device_datetime,
  battery_percentage = NEW.device_battery_percent,
 gsm_status = NEW.gsm_status,
 gpssignal = NEW.gpssignal
  WHERE deviceimei = @deviceimei;
  END IF;
 /* IO_STATE -3 : HEART PACKET WITH BATTERY*/

 /* END - LIVE DATA UPDATION */

 /* GENERIC REPORTS - STARTS HERE */
 /* KEYON KEY OFF REPORT - STARTS HERE */
 IF((@ignition_report_flag = 1 OR @ignition_report_flag = 0 OR @ignition_report_flag IS NULL) AND (NEW.ignition = 1) AND NEW.packet_status = 0 ) AND NEW.lattitute!= '0.000000' AND NEW.longitute!= '0.000000' THEN
 /* LIVE DATA UPDATING */
 UPDATE live_data
 SET
 ignition_report_flag = 2,
 last_ignition_on_time = NEW.device_datetime,
 last_ignition_off_time = NEW.device_datetime,
 ignition_report_datetime = NEW.device_datetime,
 device_battery_volt = NEW.device_battery_volt,
 vehicle_battery_volt = NEW.vehicle_battery_volt
 WHERE
 deviceimei = NEW.deviceimei;
 /* LIVE DATA UPDATING */

 /* INSERT LIVE NOTIFICATION DATA - IGINITON ON  - STARTS HERE*/
 IF (@current_alert_status!=3 OR @current_alert_status=0) THEN
 INSERT INTO twings.live_notifications(`vehicle_id`, `device_imei`, `alert_type_id`, `lattitute`, `longitute`, `ignition`, `speed`, `angle`, `odometer`, `device_updatedtime`, `ignition_flag`, `user_id`, `created_at`, `updated_at`) VALUES (@vehicle_id,@deviceimei,3,@lattitute,@longitute,NEW.ignition,NEW.speed,NEW.angle,current_odometer,NEW.device_datetime,1,@client_id,now(),now());
 UPDATE live_data
 SET
 current_alert_status=3 WHERE deviceimei = NEW.deviceimei;
 END IF;
 /* INSERT LIVE NOTIFICATION DATA - IGINITON ON - ENDS HERE */

 /* KEYON KEY OFF REPORT - STARTS HERE */
 INSERT INTO keyoff_keyon_reports(flag,start_latitude,start_longitude,start_datetime,device_imei,vehicle_id,start_odometer,client_id)VALUES(1,NEW.lattitute,NEW.longitute,NEW.device_datetime,NEW.deviceimei,@vehicle_id,@odometer,@client_id);
 /* KEYON KEY OFF REPORT - ENDS HERE */

 /* PARKING REPORT - STARTS HERE */
 UPDATE parking_reports SET flag = 2,end_datetime = NEW.device_datetime,end_latitude = NEW.lattitute,end_longitude = NEW.longitute WHERE device_imei = NEW.deviceimei AND flag = 1;
 /* PARKING REPORT - ENDS HERE */

 END IF;


 /* START - INSERT IDLE REPORT*/
 IF(ROUND(NEW.speed) = 0 AND @ignition_report_flag = 2 AND NEW.ignition = 1 AND NEW.packet_status = 0 ) AND NEW.lattitute!= '0.000000' AND NEW.longitute!= '0.000000'  THEN
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
 /* END - INSERT IDLE REPORT*/
 /* START - PARKING REPORT*/
  IF(@ignition_report_flag = 2 OR @ignition_report_flag = 3) AND NEW.ignition = 0 AND NEW.packet_status = 0 AND NEW.io_state!=1  THEN
  UPDATE
                             live_data
                             SET
                             vehicle_current_status=1,
                             ignition_report_flag = 0,
                             last_ignition_on_time = NEW.device_datetime,
                             last_ignition_off_time = NEW.device_datetime
                             WHERE
                             deviceimei = NEW.deviceimei;
 IF (@current_alert_status!=4 OR @current_alert_status=0) THEN
 INSERT INTO twings.live_notifications(`vehicle_id`, `device_imei`, `alert_type_id`, `lattitute`, `longitute`, `ignition`, `speed`, `angle`, `odometer`, `device_updatedtime`, `ignition_flag`, `user_id`, `created_at`, `updated_at`) VALUES (@vehicle_id,@deviceimei,4,@lattitute,@longitute,NEW.ignition,NEW.speed,NEW.angle,current_odometer,NEW.device_datetime,0,@client_id,now(),now());
 UPDATE live_data SET current_alert_status=4 WHERE deviceimei = NEW.deviceimei;
 END IF;
                             UPDATE
                             keyoff_keyon_reports
                             SET
                             flag = 2,
                             end_datetime = NEW.device_datetime,
                             end_latitude = @lattitute,
                             end_longitude = @longitute,
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
                             @lattitute,
                             @longitute,
                             NEW.device_datetime,
                             NEW.deviceimei,
                             @vehicle_id
                             );
                             UPDATE
                             idle_reports
                             SET
                             flag = 2,
                             end_datetime = NEW.device_datetime,
                             end_latitude = @lattitute,
                             end_longitude = @longitute
                             WHERE
                             device_imei = NEW.deviceimei AND flag = 1;
  END IF;
  /* END - PARKING REPORT*/
  IF @ignition_report_flag = 3 AND NEW.ignition = 1 AND NEW.lattitute!=0.0 AND NEW.longitute!=0.0 AND NEW.lattitute!='0.00000000' AND NEW.longitute!='0.00000000' AND NEW.speed>1 AND NEW.packet_status=0  THEN
   UPDATE
                             idle_reports
                             SET
                             flag = 2,
                             end_datetime = NEW.device_datetime,
                             end_latitude = NEW.lattitute,
                             end_longitude = NEW.longitute
                             WHERE
                             device_imei = NEW.deviceimei AND flag = 1;
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

 END IF;
 /* START - PLAYBACK HISTORY*/
 IF NEW.ignition = 1 AND NEW.lattitute!=0.0 AND NEW.longitute!=0.0 AND NEW.lattitute!='0.00000000' AND NEW.longitute!='0.00000000' AND NEW.speed>1  THEN
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
                     packet_status,
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
                 @odometer,
                 NEW.packet_status,
                 @client_id
                 );
 END IF;
 /* END - PLAYBACK HISTORY*/
 /* GENERIC REPORTS - ENDS HERE */
 IF NEW.packet_status=0  THEN
     IF NEW.ignition=1 AND NEW.speed=0 THEN
         SET current_vehicle_status=2;
     ELSEIF NEW.ignition=1 AND NEW.speed>1 THEN
         SET current_vehicle_status=3;
     ELSEIF NEW.ignition=0 AND NEW.speed=0 THEN
         SET current_vehicle_status=1;
     END IF;
     /* UPDATING VEHICLE CURRENT STATUS - STARTS HERE */
         UPDATE
         live_data
         SET
         vehicle_current_status=current_vehicle_status
         WHERE
         deviceimei = NEW.deviceimei;
     /* UPDATING VEHICLE CURRENT STATUS - END HERE */
 /* LIVE ALERT UPDATION STARTS HERE*/
 SELECT parking_alert_time,idle_alert_time,speed_limit INTO @parking_alert_time,@idle_alert_time,@speed_limit FROM twings.configurations WHERE  device_imei = NEW.deviceimei ORDER BY device_imei DESC LIMIT 1;
 IF (@current_alert_status!=20 OR @current_alert_status=0) AND current_vehicle_status=1 AND @diff_time>@parking_alert_time THEN
 INSERT INTO twings.live_notifications(`vehicle_id`, `device_imei`, `alert_type_id`, `lattitute`, `longitute`, `ignition`, `speed`, `angle`, `odometer`, `device_updatedtime`, `ignition_flag`, `user_id`, `created_at`, `updated_at`) VALUES (@vehicle_id,@deviceimei,20,NEW.lattitute,NEW.longitute,NEW.ignition,NEW.speed,NEW.angle,current_odometer,NEW.device_datetime,0,@client_id,now(),now());
 UPDATE live_data SET current_alert_status=20 WHERE deviceimei = NEW.deviceimei;
 END IF;
 IF (@current_alert_status!=21 OR @current_alert_status=0) AND current_vehicle_status= 2 AND @diff_time>@idle_alert_time THEN
 INSERT INTO twings.live_notifications(`vehicle_id`, `device_imei`, `alert_type_id`, `lattitute`, `longitute`, `ignition`, `speed`, `angle`, `odometer`, `device_updatedtime`, `ignition_flag`, `user_id`, `created_at`, `updated_at`) VALUES (@vehicle_id,@deviceimei,21,NEW.lattitute,NEW.longitute,NEW.ignition,NEW.speed,NEW.angle,current_odometer,NEW.device_datetime,0,@client_id,now(),now());
 UPDATE live_data SET current_alert_status=21 WHERE deviceimei = NEW.deviceimei;
 END IF;
 IF (@current_alert_status!=5 OR @current_alert_status=0) AND current_vehicle_status=3 AND NEW.speed < @speed_limit THEN
 INSERT INTO twings.live_notifications(`vehicle_id`, `device_imei`, `alert_type_id`, `lattitute`, `longitute`, `ignition`, `speed`, `angle`, `odometer`, `device_updatedtime`, `ignition_flag`, `user_id`, `created_at`, `updated_at`) VALUES (@vehicle_id,@deviceimei,5,NEW.lattitute,NEW.longitute,NEW.ignition,NEW.speed,NEW.angle,current_odometer,NEW.device_datetime,1,@client_id,now(),now());
 UPDATE live_data SET current_alert_status=5 WHERE deviceimei = NEW.deviceimei;

 IF (@current_alert_status = 5) AND current_vehicle_status=3 AND NEW.speed > @speed_limit THEN
  UPDATE live_data SET current_alert_status=0 WHERE deviceimei = NEW.deviceimei;
 END IF;
 /* ADD OVERSPEED REPORT HERE */
 END IF;

  IF (@current_alert_status!=1 OR @current_alert_status=0) AND NEW.ac_status=1 AND (@ac_flag IS NULL OR @ac_flag = 0) AND (NEW.io_state!=1) THEN
 INSERT INTO twings.live_notifications(`vehicle_id`, `device_imei`, `alert_type_id`, `lattitute`, `longitute`, `ignition`, `speed`, `angle`, `odometer`, `device_updatedtime`, `ignition_flag`, `user_id`, `created_at`, `updated_at`) VALUES (@vehicle_id,@deviceimei,1,NEW.lattitute,NEW.longitute,NEW.ignition,NEW.speed,NEW.angle,current_odometer,NEW.device_datetime,NEW.ignition,@client_id,now(),now());
 UPDATE live_data SET ac_status=1,current_alert_status=1,ac_flag=1 WHERE deviceimei = NEW.deviceimei;
 INSERT INTO `ac_reports`(`vehicle_id`, `device_imei`, `start_latitude`, `start_longitude`, `start_datetime`,`start_odometer`,`flag`, `created_at`) VALUES (@vehicle_id,@device_imei,NEW.lattitute,NEW.longitute, NEW.device_datetime,current_odometer,1,now());
 END IF;
 IF (@current_alert_status!=2 OR @current_alert_status=0) AND NEW.ac_status=0  AND @ac_flag = 1 AND (NEW.io_state!=1) THEN
 INSERT INTO twings.live_notifications(`vehicle_id`, `device_imei`, `alert_type_id`, `lattitute`, `longitute`, `ignition`, `speed`, `angle`, `odometer`, `device_updatedtime`, `ignition_flag`, `user_id`, `created_at`, `updated_at`) VALUES (@vehicle_id,@deviceimei,2,NEW.lattitute,NEW.longitute,NEW.ignition,NEW.speed,NEW.angle,current_odometer,NEW.device_datetime,NEW.ignition,@client_id,now(),now());
 UPDATE live_data SET ac_status=0,current_alert_status=2,ac_flag=0 WHERE deviceimei = NEW.deviceimei;
 UPDATE `ac_reports` SET `end_latitude`=NEW.lattitute,`end_longitude`=NEW.longitute,`end_datetime`=NEW.device_datetime,`end_odometer`=current_odometer,`flag`=2,`updated_at`=now() WHERE device_imei=NEW.deviceimei;
 END IF;
 /* LIVE ALERT UPDATION ENDS HERE*/
 END IF;
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
