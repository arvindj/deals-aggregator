<?php
class dbi
{
        var $dbWrite = null;
        var $dbRead = null;
        var $CI;
        function dbi()
        {
                $this->CI = & get_instance();
                $this->dbRead = $this->dbWrite = $this->CI->db;
        }

        function _select($tableName, $selectFields, $whereData, $limitVal = false, $orderBy=false)
        {
                if($orderBy)
                $this->dbRead->orderby($orderBy);

                if($limitVal)
                {
                        $count = $limitVal['count'];
                        $offset = isset($limitVal['offset'])?$limitVal['offset']:0;
                        $this->dbRead->limit($count, $offset);
                }
                $query = $this->dbRead->select($selectFields)->from($tableName)->where($whereData)->get();

                return ($query->num_rows()>0)?$query->result_array():false;
        }

        function _insert($tableName, $insertArr)
        {
                $this->dbWrite->insert($tableName, $insertArr);
        }

        function _update($tableName, $updateArr, $whereArr)
        {
                $this->dbWrite->update($tableName, $updateArr, $whereArr);
        }

        function _delete($tableName, $whereArr)
        {
                $this->dbWrite->delete($tableName, $whereArr);
        }
}
?>
