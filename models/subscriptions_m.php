<?php

class Subscriptions_m extends ForumsBase_m {

    protected $_table = 'subscriptions';
    protected $_stream = 'subscriptions';

    /**
    * Adds a suscription
    *
    * @param int $user_id
    * @param int $topic_id
    */
    function add($user_id, $post_id)
    {
        if( ! $this->is_subscribed($user_id, $post_id) )
        {
            return $this->insert(array('user_id' => $user_id, 'post_id' => $post_id));
        }
    }

    function is_subscribed($user_id, $post_id)
    {
        return ! $this->count_by(array('user_id' => $user_id, 'post_id' => $post_id)) > 0;
    }

    function delete_subscriptions_by_posts($ids)
    {
        return $this->db->where_in('post_id', $ids)->delete( $this->table_name() );
    }
}