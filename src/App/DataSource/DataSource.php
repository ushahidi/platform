<?php

namespace Ushahidi\App\DataSource;

/**
 * Base Interface for all Data Source
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
interface DataSource
{

    /**
     * Constructor function for DataSource
     */
    // @todo add state store
    public function __construct(array $config);

    public function getName();
    public function getId();
    public function getServices();
    public function getOptions();
    public function getInboundFields();
    public function getInboundFormId();
    public function getInboundFieldMappings();
    public function isUserConfigurable();
}
