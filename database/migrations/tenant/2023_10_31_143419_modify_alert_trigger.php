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
        CREATE TRIGGER `live_alert` AFTER INSERT ON `alert_status`
 FOR EACH ROW
 BEGIN
  SELECT deviceimei,client_id,vehicle_id,vehicle_current_status,vehicle_status,odometer,ignition_report_flag,ignition_report_datetime,device_battery_volt,vehicle_battery_volt,lattitute,longitute,device_updatedtime,speed,TIMESTAMPDIFF(
     MINUTE,
     ignition_report_datetime,
     NEW.device_datetime
   ),current_alert_status,ac_flag
             INTO @deviceimei,@client_id,@vehicle_id,@vehicle_current_status,@vehicle_status,@odometer,@ignition_report_flag,@ignition_report_datetime,@device_battery_volt,@vehicle_battery_volt,@lattitute,@longitute,@device_updatedtime,@speed,@diff_time,@current_alert_status,@ac_flag
             FROM live_data
             WHERE
             deviceimei = NEW.deviceimei  ORDER BY  vehicle_id DESC LIMIT 1;


   /* CHECK THE ALERT TYPE */
   IF NEW.alert_type_id>5 AND @current_alert_status!=NEW.alert_type_id  THEN
    INSERT INTO twings.live_notifications(`vehicle_id`, `device_imei`, `alert_type_id`, `lattitute`, `longitute`, `ignition`, `speed`, `angle`, `odometer`, `device_updatedtime`, `ignition_flag`, `user_id`, `created_at`, `updated_at`) VALUES (@vehicle_id,@deviceimei,NEW.alert_type_id,@lattitute,@longitute,@ignition,@speed,@angle,@odometer,@device_updatedtime,1,@client_id,now(),now());
 UPDATE live_data
 SET
 current_alert_status=NEW.alert_type_id WHERE deviceimei = NEW.deviceimei;
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
