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
        $query = "select v_domains.domain_name, users_count, ext_count, dev_count, dest_count, cc_count, vmails_count from v_domains
        left join (select domain_uuid as users_domain, count(*) as users_count from v_users group by domain_uuid) a on (a.users_domain=domain_uuid)
        left join (select domain_uuid as ext_domain, count(*) as ext_count from v_extensions group by domain_uuid) b on (b.ext_domain=domain_uuid)
        left join (select domain_uuid as dev_domain, count(*) as dev_count from v_devices group by domain_uuid) c on (c.dev_domain=domain_uuid)
        left join (select domain_uuid as dest_domain, count(*) as dest_count from v_destinations group by domain_uuid) d on (d.dest_domain=domain_uuid)
        left join (select domain_uuid as cc_domain, count(*) as cc_count from v_call_center_queues group by domain_uuid) b on (b.cc_domain=domain_uuid)
        left join (select domain_uuid as vmail_domain, count(*) as vmails_count from v_voicemail_messages group by domain_uuid) f on (f.vmail_domain=domain_uuid)";

       return $this->db->select($query);

    }


}