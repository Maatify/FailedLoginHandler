<?php
/**
 * @PHP       Version >= 8.0
 * @copyright ©2024 Maatify.dev
 * @author    Mohamed Abdulalim (megyptm) <mohamed@maatify.dev>
 * @since     2024-04-18 7:A5 PM
 * @link      https://www.maatify.dev Maatify.com
 * @link      https://github.com/Maatify/FailedLoginHandler  view project on GitHub
 * @Maatify   DB :: FailedLoginHandler
 */

namespace Maatify\FailedLoginHandler;

use \App\Assist\AppFunctions;
use \App\DB\DBS\DbConnector;
use \App\DB\Tables\Admin\AdminLoginToken;
use Maatify\Functions\GeneralFunctions;
use Maatify\Json\Json;

abstract class FailedLoginHandler extends DbConnector
{
    private string $ip;

    protected int $tries = 15;

    protected string $col_name = 'username';

    public function __construct()
    {
        parent::__construct();
        $this->ip = AppFunctions::IP();
    }

    public function CheckFailed(): void
    {
        $this->IsFailedRecord();
    }

    private function IsFailed(string $ip = ''): int
    {
        //        $time = date("Y-m-d H:i:s", strtotime('-1 days')); // -1 hours
        if(empty($ip)){
            $ip = $this->ip;
        }

        return $this->CountTableRows("`$this->tableName`
			LEFT JOIN `$this->tableName` as success
            	ON `success`.`isSuccess` AND
                	`success`.`ip` = '$ip' AND
                	`success`.`id` = (select `id` FROM `$this->tableName`
                                      WHERE `isSuccess` AND `id` = `$this->tableName`.`id` AND `ip` = '$ip' ORDER BY `id` DESC LIMIT 1)

            LEFT JOIN `$this->tableName` AS fl
            	ON `fl`.`id` > ifnull(`success`.`id`,0) AND
                `fl`.`ip` = '$ip'",
            '`fl`.`id`',
            "`$this->tableName`.`ip` = '$ip' 
            	GROUP By `$this->tableName`.`id`
                ORDER BY `$this->tableName`.`id` DESC LIMIT 1");
    }

    private function IsFailedRecord(string $ip = ''): int
    {
        $check = $this->IsFailed($ip);
        if(!empty($check)){
            if($check >= $this->tries){
                Json::UnauthorizedBlock();
                exit();
            }else{
                return $check;
            }
        }else{
            return 0;
        }
    }

    public function SuccessByAdmin(string $ip, string $username): bool
    {
        if($this->IsFailedRecord($ip)){
            return $this->Record(1, $ip, $username,AdminLoginToken::obj()->GetAdminID());
        }else{
            Json::NotExist('ip', line: debug_backtrace()[0]['line']);
        }
        return false;
    }

    public function Failed(string $username): bool
    {
        $check = $this->IsFailedRecord();
        if($check < $this->tries){
            return $this->Record(0, $this->ip, $username, 0);
        }
        return false;
    }

    public function Success($username): bool
    {

        if($this->IsFailedRecord()){
            return $this->Record(1, $this->ip, $username, 0);
        }
        return false;
    }

    private function Record(int $is_success, string $ip, string $username, int $admin_id): int
    {
        return $this->Add([
            'isSuccess'     => $is_success,
            'ip'            => $ip,
            $this->col_name => strtolower($username),
            'time'          => AppFunctions::CurrentDateTime(),
            'page'          => GeneralFunctions::CurrentPage() . (! empty($_GET['action']) ? '/' . $_GET['action'] : ''),
            'admin_id'      => $admin_id,
        ]);
    }

    public function Log(string $username, string $ip): array
    {
        $where = $this->PrepareWhere($username, $ip);
        return $this->PaginationHandler(
        // Count
            $this->CountThisTableRows(
                'id',
                "$where"),

            // Filter
            $this->PaginationRows("`$this->tableName`",
                " `$this->tableName`.*",
                " $where ORDER BY `$this->tableName`.`id` DESC "),
        );
    }

    private function PrepareWhere(string $username, string $ip): string
    {
        $where = "";
        if(!empty($username)){
            $where .= " LCASE(`$this->tableName`.`$this->col_name`) = LCASE('$username') OR ";
        }
        if(!empty($ip)){
            $where .= " `$this->tableName`.`ip` = '$ip' OR ";
        }
        $where = rtrim($where, " OR ");

        if(empty($where)){
            $where = " `$this->tableName`.`id` > '0' ";
        }
        return $where;
    }


}