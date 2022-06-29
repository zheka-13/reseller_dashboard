<?php

class DomainStatService
{

    private $db;

    /**
     * @param $db
     */
    public function __construct($db)
    {

        $this->db = $db;
    }

    public function getDomainsStat()
    {
        $query = "select v_domains.domain_name, users_count, cc_count, cr_count, gates_count, rooms_count, vmails_count from v_domains
        left join (select domain_uuid as users_domain, count(*) as users_count from v_users group by domain_uuid) a on (a.users_domain=domain_uuid)
        left join (select domain_uuid as cc_domain, count(*) as cc_count from v_call_center_queues group by domain_uuid) b on (b.cc_domain=domain_uuid)
        left join (select domain_uuid as cr_domain, count(*) as cr_count from v_call_recordings group by domain_uuid) c on (c.cr_domain=domain_uuid)
        left join (select domain_uuid as gate_domain, count(*) as gates_count from v_gateways group by domain_uuid) d on (d.gate_domain=domain_uuid)
        left join (select domain_uuid as room_domain, count(*) as rooms_count from v_conference_rooms group by domain_uuid) e on (e.room_domain=domain_uuid)
        left join (select domain_uuid as vmail_domain, count(*) as vmails_count from v_voicemail_messages group by domain_uuid) f on (f.vmail_domain=domain_uuid)";

       return $this->db->select($query);

    }


}