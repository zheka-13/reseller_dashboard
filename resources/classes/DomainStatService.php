<?php

class DomainStatService
{

    private $db;
    const TYPE_HOUR = 'hour';
    const TYPE_DAY = 'day';

    /**
     * @param $db
     */
    public function __construct($db)
    {

        $this->db = $db;
    }

    public function getDomainsStat()
    {
        $query = "select v_domains.domain_name, users_count, ext_count, dev_count, dest_count, cc_count, vmails_count from v_domains
        left join (select domain_uuid as users_domain, count(*) as users_count from v_users group by domain_uuid) a on (a.users_domain=domain_uuid)
        left join (select domain_uuid as ext_domain, count(*) as ext_count from v_extensions group by domain_uuid) b on (b.ext_domain=domain_uuid)
        left join (select domain_uuid as dev_domain, count(*) as dev_count from v_devices group by domain_uuid) c on (c.dev_domain=domain_uuid)
        left join (select domain_uuid as dest_domain, count(*) as dest_count from v_destinations group by domain_uuid) d on (d.dest_domain=domain_uuid)
        left join (select domain_uuid as cc_domain, count(*) as cc_count from v_call_center_queues group by domain_uuid) e on (e.cc_domain=domain_uuid)
        left join (select domain_uuid as vmail_domain, count(*) as vmails_count from v_voicemail_messages group by domain_uuid) f on (f.vmail_domain=domain_uuid)";

       return $this->db->select($query);

    }

    public function getHourlyChartData()
    {
        return $this->getChartData(self::TYPE_HOUR);
    }

    public function getDailyChartData()
    {
        return $this->getChartData(self::TYPE_DAY);
    }

    private function getChartData($type)
    {
        $data = [];
        $data['labels'] = $this->getLabels($type);
        $stat = $this->getData($type, $data['labels']);
        $data['inbound'] = $stat['inbound'];
        $data['outbound'] = $stat['outbound'];
        return $data;
    }

    private function getLabels($type)
    {
        $labels = [];
        $query = "select date_trunc('hour', dtime)::timestamp(0) as dtime from (select * from generate_series(now()-'1 day'::interval, now(), '1 hour') as dtime) d
            order by dtime asc";

        if ($type == self::TYPE_DAY){
            $query = "select dtime::date as dtime from (select * from generate_series(now()-'10 days'::interval, now(), '1 day') as dtime) d
                order by dtime asc";
        }
        $data = $this->db->select($query);
        foreach ($data as $row){
            if ($type == self::TYPE_HOUR){
                $row['dtime'] = substr($row['dtime'], 0, -3);
            }
            $labels[] = $row['dtime'];
        }
        return $labels;

    }

    private function getData($type, $labels)
    {
        $query = "SELECT direction, count(*) as cnt, date_trunc('hour', start_stamp)::timestamp(0) as dtime  FROM public.v_xml_cdr
                where direction in ('inbound', 'outbound') and start_stamp > now()-'1 day'::interval
                group by direction,  date_trunc('hour', start_stamp)
                ORDER BY dtime ASC";

        if ($type == self::TYPE_DAY){
            $query = "SELECT direction, count(*) as cnt, start_stamp::date as dtime  FROM public.v_xml_cdr
                where direction in ('inbound', 'outbound') and start_stamp > now()-'10 days'::interval
                group by direction,  start_stamp::date
                ORDER BY dtime ASC";
        }

        $data = $this->db->select($query);
        $call_data = [];
        foreach ($data as $row){
            if ($type == self::TYPE_HOUR){
                $row['dtime'] = substr($row['dtime'], 0, -3);
            }
            if (!isset($call_data[$row['dtime']])){
                $call_data[$row['dtime']] = [];
            }
            if (!isset($call_data[$row['dtime']][$row['direction']])){
                $call_data[$row['dtime']][$row['direction']] = $row['cnt'];
            }
        }
        $res = [
            "inbound" => [],
            "outbound" => []
        ];
        foreach ($labels as $label){
            if (isset($call_data[$label]['inbound'])){
                $res['inbound'][] = $call_data[$label]['inbound'];
            }
            else{
                $res['inbound'][] = 0;
            }
            if (isset($call_data[$label]['outbound'])){
                $res['outbound'][] = $call_data[$label]['outbound'];
            }
            else{
                $res['outbound'][] = 0;
            }
        }
        return $res;

    }


}