<?php

namespace OCA\OJSXC\StanzaHandlers;

use OCA\OJSXC\Db\IQRoster;
use OCA\OJSXC\Db\MessageMapper;
use OCP\IUserManager;
use Sabre\Xml\Reader;
use Sabre\Xml\Writer;

class IQ extends StanzaHandler {

	private $type;

	private $id;

	private $query;

	public function __construct($userId, $host, IUserManager $userManager) {
		parent::__construct($userId, $host);
		$this->userManager = $userManager;
	}


	public function handle($stanza) {
		$this->to = $this->getAttribute($stanza, 'to');

		foreach($stanza['value'] as $value){
			if ($value['name'] === '{jabber:iq:roster}query'){
				$id = $stanza['attributes']['id'];
				$iqRoster = new \OCA\OJSXC\Db\IQRoster();
				$iqRoster->setType('result');
				$iqRoster->setTo($this->from);
				$iqRoster->setQid($id);
				foreach($this->userManager->search('') as $user){
					if($user->getUID() !== $this->userId) {
						$iqRoster->addItem($user . '@' . $this->host, $user->getDisplayName());
					}
				}
				return $iqRoster;
			}
		}

	}

}