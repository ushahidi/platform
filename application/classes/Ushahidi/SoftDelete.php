<?php

/**
 * Ushahidi SoftDelete Trait
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

trait Ushahidi_SoftDelete
{
    /**
     * Set your own soft delete column name
     *
     * @return String name of the column
     */
    abstract protected function _get_soft_delete_column();

    public function delete()
    {
        $this->{$this->_get_soft_delete_column()} = true;
        $this->save();
        return $this;
    }

    public function undelete()
    {
        $this->{$this->_get_soft_delete_column()} = false;
        $this->save();
        return $this;
    }
}
