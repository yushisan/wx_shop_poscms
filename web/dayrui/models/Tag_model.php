<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tag_model extends CI_Model {

    public $link;
    public $tablename;

    /*
     * Tag模型类
     */
    public function __construct() {
        parent::__construct();

        $this->tablename = $this->db->dbprefix(SITE_ID.'_'.APP_DIR.'_tag');
    }

    /*
     * 数据分页显示
     *
     * @param	string	$kw		关键字参数
     * @param	intval	$page	页数
     * @param	intval	$total	总数据
     * @return	array
     */
    public function limit_page($kw, $page, $total) {

        if (!$total) {
            $select	= $this->db->select('count(*) as total');
            $kw && $select->like('name', urldecode($kw));
            $data = $select->get($this->tablename)->row_array();
            unset($select);
            $total = (int)$data['total'];
            if (!$total) {
                return array(array(), array('total' => 0, 'kw' => $kw));
            }
        }

        $select	= $this->db->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
        $kw && $select->like('name', urldecode($kw));

        $_order = isset($_GET['order']) && strpos($_GET['order'], "undefined") !== 0 ? $this->input->get('order') : 'id DESC';
        $data = $select->order_by($_order)->get($this->tablename)->result_array();

        return array($data, array('total' => $total, 'kw' => $kw));
    }

    /*
     * 获取标签详细
     *
     * @param	string	$code
     * @return	array
     */
    public function tag($code) {

        if (!$code) {
            return NULL;
        }

        $this->db->where('code', $code)->set('hits','hits+1',FALSE)->update($this->tablename);

        return $this->db->where('code', $code)->get($this->tablename)->result_array();
    }

    /*
     * 获取标签
     *
     * @param	intval	$id
     * @return	array
     */
    public function get($id) {

        if (!$id) {
            return NULL;
        }

        return $this->db->where('id', $id)->limit(1)->get($this->tablename)->row_array();
    }

    /*
     * 添加tag
     *
     * @return	id
     */
    public function add($data) {

        if (!$data) {
            return -1;
        } elseif ($this->db->where('name', $data['name'])->count_all_results($this->tablename)) {
            return -2;
        }

        $this->db->insert($this->tablename, array(
            'name' => $data['name'],
            'code' => $data['code'],
            'hits' => (int)$data['hits']
        ));

        return $this->db->insert_id();
    }

    /*
     * 修改
     *
     * @param	intval	$id
     * @param	array	$data
     * @return	intavl
     */
    public function edit($id, $data) {

        if (!$data || !$id) {
            return -1;
        } elseif ($this->db->where('id<>', $id)->where('name', $data['name'])->count_all_results($this->tablename)) {
            return -2;
        }

        $this->db->where('id', $id)->update($this->tablename, array(
            'name' => $data['name'],
            'code' => $data['code'],
            'hits' => (int)$data['hits']
        ));

        $this->ci->clear_cache(APP_DIR.'-'.SITE_ID.'-tag-'.$data['name']);

        return $id;
    }




    // 检查别名是否可用
    public function check_code($id, $value) {

        if (!$value) {
            return 1;
        }

        return $this->db->where('id<>', $id)->where('code', $value)->count_all_results(SITE_ID.'_tag');
    }

    // 检查名称是否可用
    public function check_name($id, $value) {

        if (!$value) {
            return 1;
        }

        return $this->db->where('id<>', $id)->where('name', $value)->count_all_results(SITE_ID.'_tag');
    }

    // 批量
    public function save_all_data($pid, $data) {

        $c = 0;
        $names = explode(PHP_EOL, trim($data));
        foreach ($names as $t) {
            $t = trim($t);
            if (!$t) {
                continue;
            } elseif ($this->check_name(0, $t)) {
                return $this->ci->admin_msg(fc_lang('词【%s】已经存在', $t));
            }
            $cname = dr_word2pinyin($t);
            $count = $this->db->where('code', $cname)->count_all_results(SITE_ID.'_tag');
            $code = $count ? $cname.$count : $cname;
            $pcode = $this->get_pcode(array('pid' => $pid, 'code' => $code));
            $rt = $this->db->insert(SITE_ID.'_tag', array(
                'pid' => $pid,
                'name' => $t,
                'code' => $code,
                'pcode' => $pcode,
                'hits' => 0,
                'total' => 0,
                'displayorder' => 0,
                'childids' => '',
                'content' => '',
                'url' => '',
            ));
            if (!$rt) {
                continue;
            }
            $c++;
        }
        return fc_lang('批量添加%s个', $c);
    }



    // 获取pcode
    public function get_pcode($data) {

        if (!$data['pid']) {
            return $data['code'];
        }

        $row = $this->db->where('id', $data['pid'])->limit(1)->get(SITE_ID.'_tag')->row_array();

        return trim($row['code'].'/'.$data['code'], '/');

    }

    // 缓存
    public function cache($siteid = SITE_ID) {

        $cache = $field = array();

        // 判断表是否存在
        if (!$this->db->table_exists($siteid.'_tag'))
        {
            $this->db->query(trim("
        CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix($siteid.'_tag')."` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `pid` int(10) DEFAULT '0' COMMENT '父级id',
          `name` varchar(200) NOT NULL COMMENT '关键词名称',
          `code` varchar(200) NOT NULL COMMENT '关键词代码（拼音）',
          `pcode` varchar(255) DEFAULT NULL,
          `hits` mediumint(8) unsigned NOT NULL COMMENT '点击量',
          `url` varchar(255) DEFAULT NULL COMMENT '关键词url',
          `childids` varchar(255) NOT NULL COMMENT '子类集合',
          `content` text NOT NULL COMMENT '关键词描述',
          `total` int(10) NOT NULL COMMENT '点击数量',
          `displayorder` int(10) NOT NULL COMMENT '排序值',
          PRIMARY KEY (`id`),
          UNIQUE KEY `name` (`name`),
          KEY `letter` (`code`,`hits`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关键词库表';
        "));
        }

        $result = $this->db->where('pid', 0)->order_by('displayorder DESC,id ASC')->get($siteid.'_tag')->result_array();
        if ($result) {
            foreach ($result as $data) {
                $tag = $data['name'];
                $ids = $code = array();
                $result2 = $this->db->where('pid', $data['id'])->order_by('displayorder DESC,id ASC')->get($siteid.'_tag')->result_array();
                if ($result2) {
                    foreach ($result2 as $data2) {
                        $tag.= ','.$data2['name'];
                        $ids[] = $data2['id'];
                        $code[] = $data2['code'];

                        $url = dr_tags_url($data2['code']);
                        $cache[$data2['code']] = array(
                            'id' => $data2['id'],
                            'pid' => $data['code'],
                            'name' => $data2['name'],
                            'codes' => '',
                            'tags' => $data2['name'],
                            'total' => 0,
                            'childids' => array($data2['id']),
                            'url' => $url,
                        );
                        $this->db->where('id', $data2['id'])->update($siteid.'_tag', array(
                            'url' => $url,
                            'total' => 0
                        ));
                    }
                }

                $url = dr_tags_url($data['pcode']);
                $cache[$data['code']] = array(
                    'id' => $data['id'],
                    'pid' => '',
                    'name' => $data['name'],
                    'tags' => trim($tag, ','),
                    'codes' => $code,
                    'total' => count($result2),
                    'childids' => $ids,
                    'url' => $url,
                );

                $this->db->where('id', $data['id'])->update($siteid.'_tag', array(
                    'url' => $url,
                    'total' => $cache[$data['code']]['total']
                ));
            }
        }

        $temp = $this->db->where('relatedname', 'tag')->where('relatedid', $siteid)->get('field')->result_array();
        if ($temp) {
            foreach ($temp as $t) {
                $t['textname'] = $t['name'];
                $t['setting'] = dr_string2array($t['setting']);
                $field[$t['fieldname']] = $t;
            }
        }


        // 写入缓存
        $this->ci->dcache->set('tags-'.$siteid, $cache);
        $this->ci->dcache->set('tags-field-'.$siteid, $field);

        return $cache;
    }

}