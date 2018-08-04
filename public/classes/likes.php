<?php 

class Likes extends DBEntity
{
    private $text_id;
    private $learning_lang_id;
    
    /**
     * Constructor
     * 
     * Sets 3 basic variables used to identify any text: $con, $user_id & learning_lang_id
     *
     * @param mysqli_connect $con
     * @param integer $user_id
     * @param integer $learning_lang_id
     */
    public function __construct($con, $text_id, $user_id, $learning_lang_id) {
        parent::__construct($con, $user_id);
        $this->text_id = $text_id;
        $this->learning_lang_id = $learning_lang_id;
        $this->table = 'likes';
    }

    /**
     * Toggles like for a specific text
     *
     * @return mysqli|boolean
     */
    public function toggle()
    {
        $result = $this->con->query("INSERT INTO likes (likesTextId, likesUserId, likesLgId, likesLiked)
            VALUES ('$this->text_id', '$this->user_id', '$this->learning_lang_id', true) ON DUPLICATE KEY UPDATE
            likesTextId='$this->text_id', likesUserId='$this->user_id', likesLgId='$this->learning_lang_id', likesLiked=NOT likesLiked");

        return $result;
    }
}



?>