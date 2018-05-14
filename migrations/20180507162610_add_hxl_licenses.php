<?php

use Phinx\Migration\AbstractMigration;

class AddHxlLicenses extends AbstractMigration
{

    protected $licenses;

    public function up()
    {
        $this->licenses = $this->getLicenses();
        $pdo = $this->getAdapter()->getConnection();
        $insert = $pdo->prepare(
            "INSERT into
                hxl_license
                (`code`, `name`, `link`)
            VALUES
            	(:code, :name, :link)
			"
        );
        foreach ($this->licenses as $item) {
            $insert->execute(
                [
                    ':code' => $item['id'],
                    ':link' => $item['url'],
                    ':name' => $item['title']
                ]
            );
        }
    }

    public function down()
    {
        $pdo = $this->getAdapter()->getConnection();
        $codes = implode(",", array_map(function ($license) {
            return '"' . $license['id'] . '"';
        }, $this->getLicenses()));
        $delete = $pdo->prepare(
            "DELETE FROM hxl_license WHERE hxl_license.code IN ($codes)"
        );
        $delete->execute();
    }

    /**
     * Returns a list of licenses from /license_list
     * V1 will pull this daily from hdx into this db
     * @return array
     */
    private function getLicenses()
    {
        return json_decode(
            '[
				{
					"status": "active",
					"maintainer": "",
					"od_conformance": "not reviewed",
					"family": "",
					"osd_conformance": "not reviewed",
					"domain_data": "False",
					"title": "Creative Commons Attribution for Intergovernmental Organisations",
					"url": "http://creativecommons.org/licenses/by/3.0/igo/legalcode",
					"is_generic": "False",
					"is_okd_compliant": false,
					"is_osi_compliant": false,
					"domain_content": "False",
					"domain_software": "False",
					"id": "cc-by-igo"
				},
				{
					"status": "active",
					"maintainer": "",
					"od_conformance": "approved",
					"family": "",
					"osd_conformance": "not reviewed",
					"domain_data": "False",
					"title": "Creative Commons Attribution",
					"url": "http://www.opendefinition.org/licenses/cc-by",
					"is_generic": "False",
					"is_okd_compliant": true,
					"is_osi_compliant": false,
					"domain_content": "False",
					"domain_software": "False",
					"id": "cc-by"
				},
				{
					"status": "active",
					"maintainer": "",
					"od_conformance": "approved",
					"family": "",
					"osd_conformance": "not reviewed",
					"domain_data": "False",
					"title": "Creative Commons Attribution Share-Alike",
					"url": "http://www.opendefinition.org/licenses/cc-by-sa",
					"is_generic": "False",
					"is_okd_compliant": true,
					"is_osi_compliant": false,
					"domain_content": "True",
					"domain_software": "False",
					"id": "cc-by-sa"
				},
				{
					"status": "active",
					"maintainer": "",
					"od_conformance": "not reviewed",
					"family": "",
					"osd_conformance": "not reviewed",
					"domain_data": "False",
					"title": "Open Database License (ODC-ODbL)",
					"url": "",
					"is_generic": "False",
					"is_okd_compliant": false,
					"is_osi_compliant": false,
					"domain_content": "False",
					"domain_software": "False",
					"id": "hdx-odc-odbl"
				},
				{
					"status": "active",
					"maintainer": "",
					"od_conformance": "not reviewed",
					"family": "",
					"osd_conformance": "not reviewed",
					"domain_data": "False",
					"title": "Open Data Commons Attribution License (ODC-BY)",
					"url": "",
					"is_generic": "False",
					"is_okd_compliant": false,
					"is_osi_compliant": false,
					"domain_content": "False",
					"domain_software": "False",
					"id": "hdx-odc-by"
				},
				{
					"status": "active",
					"maintainer": "",
					"od_conformance": "not reviewed",
					"family": "",
					"osd_conformance": "not reviewed",
					"domain_data": "False",
					"title": "Open Data Commons Public Domain Dedication and License (PDDL)",
					"url": "",
					"is_generic": "False",
					"is_okd_compliant": false,
					"is_osi_compliant": false,
					"domain_content": "False",
					"domain_software": "False",
					"id": "hdx-pddl"
				},
				{
					"status": "active",
					"maintainer": "",
					"od_conformance": "not reviewed",
					"family": "",
					"osd_conformance": "not reviewed",
					"domain_data": "False",
					"title": "Public Domain / No Restrictions",
					"url": "",
					"is_generic": "True",
					"is_okd_compliant": false,
					"is_osi_compliant": false,
					"domain_content": "False",
					"domain_software": "False",
					"id": "other-pd-nr"
				},
				{
					"status": "active",
					"maintainer": "",
					"od_conformance": "not reviewed",
					"family": "",
					"osd_conformance": "not reviewed",
					"domain_data": "False",
					"title": "Multiple Licenses",
					"url": "",
					"is_generic": "False",
					"is_okd_compliant": false,
					"is_osi_compliant": false,
					"domain_content": "False",
					"domain_software": "False",
					"id": "hdx-multi"
				},
				{
					"status": "active",
					"maintainer": "",
					"od_conformance": "not reviewed",
					"family": "",
					"osd_conformance": "not reviewed",
					"domain_data": "False",
					"title": "Other",
					"url": "",
					"is_generic": "False",
					"is_okd_compliant": false,
					"is_osi_compliant": false,
					"domain_content": "False",
					"domain_software": "False",
					"id": "hdx-other"
				}
			]',
            true
        );
    }
}
