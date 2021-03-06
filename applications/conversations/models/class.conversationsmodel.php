<?php
/**
 * Conversations model.
 *
 * @copyright 2009-2017 Vanilla Forums Inc.
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU GPL v2
 * @package Conversations
 * @since 2.0
 */

/**
 * Introduces common methods that child classes can use.
 */
abstract class ConversationsModel extends Gdn_Model {

    use \Vanilla\FloodControlTrait;

    /**
     * @var \Vanilla\CacheInterface Object used to store the FloodControl data.
     */
    protected $floodGate;

    /**
     * Class constructor. Defines the related database table name.
     *
     * @since 2.2
     * @access public
     */
    public function __construct($name = '') {
        parent::__construct($name);
        $this->floodGate = FloodControlHelper::configure($this, 'Conversations', $this->Name);
    }

    /**
     * Checks to see if the user is spamming. Returns TRUE if the user is spamming.
     *
     * Users cannot post more than $SpamCount comments within $SpamTime
     * seconds or their account will be locked for $SpamLock seconds.
     *
     * @deprecated
     *
     * @since 2.2
     * @return bool Whether spam check is positive (TRUE = spammer).
     */
    public function checkForSpam($type, $skipSpamCheck = false) {
        deprecated(__CLASS__.' '.__METHOD__, 'FloodControlTrait::checkUserSpamming()');

        if ($skipSpamCheck) {
            return false;
        }

        $session = Gdn::session();

        // Validate $type
        if (!in_array($type, ['Conversation', 'ConversationMessage'])) {
            trigger_error(errorMessage(sprintf('Spam check type unknown: %s', $type), $this->Name, 'checkForSpam'), E_USER_ERROR);
        }

        $storageObject = FloodControlHelper::configure($this, 'Conversations', $type);
        return $this->checkUserSpamming($session->User->UserID, $storageObject);
    }

    /**
     * Get all the members of a conversation from the $conversationID.
     *
     * @param int $conversationID The conversation ID.
     *
     * @return array Array of user IDs.
     */
    public function getConversationMembers($conversationID) {
        $conversationMembers = [];

        $userConversation = new Gdn_Model('UserConversation');
        $userMembers = $userConversation->getWhere([
            'ConversationID' => $conversationID
        ])->resultArray();

        if (is_array($userMembers) && count($userMembers)) {
            $conversationMembers = array_column($userMembers, 'UserID');
        }

        return $conversationMembers;
    }

    /**
     * Check if user posting to the conversation is already a member.
     *
     * @param int $conversationID The conversation ID.
     * @param int $userID The user id.
     *
     * @return bool
     */
    public function validConversationMember($conversationID, $userID) {
        $conversationMembers = $this->getConversationMembers($conversationID);
        return (in_array($userID, $conversationMembers));
    }
}
