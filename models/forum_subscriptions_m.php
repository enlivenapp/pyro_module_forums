<?php

class Forum_subscriptions_m extends ForumsBase_m {

    protected $_table = 'subscriptions';
    protected $_stream = 'subscriptions';

    /**
    * Adds a suscription
    *
    * @param int $user_id
    * @param int $topic_id
    */
    function add($user_id, $topic_id)
    {
        if( ! $this->is_subscribed($user_id, $topic_id) )
        {
            return $this->insert(array('user_id' => $user_id, 'topic_id' => $topic_id));
        }
    }

    function is_subscribed($user_id, $topic_id)
    {
        return ! $this->count_by(array('user_id' => $user_id, 'topic_id' => $topic_id)) > 0;
    }
}