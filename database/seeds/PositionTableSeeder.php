<?php

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionTableSeeder extends Seeder
{
    /**
     * 运行职位数据填充
     *
     * @return void
     */
    public function run()
    {
        Position::create(
            [
                'id' => 1,
                'position_name' => 'boss',
                'dept_id' => '8',
            ]
        );
        Position::create(
            [
                'id' => 2,
                'position_name' => 'PHP工程师',
                'dept_id' => '1',
            ]
        );
        Position::create(
            [
                'id' => 3,
                'position_name' => 'Android工程师',
                'dept_id' => '1',
            ]
        );
        Position::create(
            [
                'id' => 4,
                'position_name' => 'IOS工程师',
                'dept_id' => '1',
            ]
        );
        Position::create(
            [
                'id' => 5,
                'position_name' => 'Android工程师',
                'dept_id' => '1',
            ]
        );
        Position::create(
            [
                'id' => 6,
                'position_name' => '运营主管',
                'dept_id' => '3',
            ]
        );
        Position::create(
            [
                'id' => 7,
                'position_name' => '运营专员',
                'dept_id' => '3',
            ]
        );
        Position::create(
            [
                'id' => 8,
                'position_name' => '市场主管',
                'dept_id' => '2',
            ]
        );
        Position::create(
            [
                'id' => 9,
                'position_name' => '市场专员',
                'dept_id' => '2',
            ]
        );
        Position::create(
            [
                'id' => 10,
                'position_name' => '客服主管',
                'dept_id' => '6',
            ]
        );
        Position::create(
            [
                'id' => 11,
                'position_name' => '客服专员',
                'dept_id' => '6',
            ]
        );
        Position::create(
            [
                'id' => 12,
                'position_name' => '商务',
                'dept_id' => '7',
            ]
        );
        Position::create(
            [
                'id' => 13,
                'position_name' => '编辑',
                'dept_id' => '3',
            ]
        );
    }
}
