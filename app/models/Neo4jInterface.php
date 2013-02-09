<?php
use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Cypher;
/**
 * This class interfaces with the Neo4j db
 */
class Neo4jInterface{
	private $_client;
	private $_wordIndex;
	private $_containsIndex; 
	private $_emailIndex;

	public function getClient(){
		return $this->_client ;
	}

	public function getWordIndex(){
		return $this->_wordIndex ;
	}

	public function getContainsIndex(){
		return $this->_containsIndex ;
	}

	public function getEmailIndex(){
		return $this->_emailIndex ;
	}
	
	public function __construct(){
		//Currently hard-coding the neo4j server to localhost port 7575.
		//This needs to be picked up from a config file.
		$this->_client = new Client('localhost', 7474) ;
		$this->_wordIndex = new NodeIndex($this->_client, 'words');
		$this->_wordIndex->save();
		$this->_emailIndex = new NodeIndex($this->_client, 'emails');
		$this->_emailIndex->save();
		$this->_containsIndex = new RelationshipIndex($this->_client,'contains');
		$this->_containsIndex->save();
	}

	/**
	 * This function stores the word node and links it to the email node
	 * @param  String $word       
	 * @param  String $emailToken 
	 * @return NodeObject $wordNode
	 */
	public function storeWordNode($word){
		if(empty($word))
			throw new Exception("Empty word or email token passed. Throwing exception", 1);
			
		$wordNode = $this->isWordNodeExists($word);
		if(!$wordNode){
			$wordNode = $this->_client->makeNode()
							 ->setProperty('type', 'word')
							 ->setProperty('value', $word)
							 ->save() ;

			$this->_wordIndex->add($wordNode,'words',$wordNode->getProperty('value'));
		}
		return $wordNode;
	}

	/**
	 * This function stores the email as a node
	 * @param  String $emailToken 
	 * @return NodeObject $emailNode
	 */
	public function storeEmailNode($emailToken){
		if(empty($emailToken)) throw new Exception("Empty email token sent. Throwing exception", 1);
		
		if(is_null($this->_emailIndex)) throw new Exception("Unable to find the email index", 1);
		$emailNode = $this->isEmailNodeExists($emailToken) ;
		if($emailNode) {
			return $emailNode;
		}

		$emailNode = $this->_client->makeNode()
						  ->setProperty('token',$emailToken)
						  ->setProperty('type', 'email')
						  ->save();
		$this->_emailIndex->add($emailNode,'emails',$emailNode->getProperty('token'));
		return $emailNode ;
	}

	/**
	 * This function stores the relationship between a word and email node
	 * @param  String $word       
	 * @param  String $emailToken 
	 * @return boolean             
	 */
	public function storeWordEmailRelation($word,$emailToken){
		if(empty($word) || empty($emailToken)) 
			throw new Exception("Empty word or email token provided. Not allowed", 1);
		
		$emailNode = $this->isEmailNodeExists($emailToken);
		if(!$emailNode)
			$emailNode = $this->storeEmailNode($emailToken);

		$wordNode = $this->isWordNodeExists($word);
		if(!$wordNode)
			$wordNode = $this->storeWordNode($word);

		//TODO: Check if the emailNode is connected to the wordNode
		$emailNode->relateTo($wordNode,'CONTAINS')->setProperty('count',1)->save();
		return true;
	}

	/**
	 * This function returns the relationship ID (if exists) between emailNode and wordNode
	 * @param  Integer $emailNode
	 * @param  Integer $wordNode 
	 * @return Integer              
	 */
	public function getRelationshipBetweenEmailAndWord($emailNode,$wordNode){
		if(empty($emailNode) || empty($wordNode))
			throw new Exception("Empty email or word node ID given. Not allowed", 1);
		// var_dump($wordNode);
		// $emailNode = $this->_client->getNode(23);
		// $wordNode = $this->_client->getNode(24);
		print_r("Here");
		var_dump($emailNode);
		$path = $emailNode->findPathsTo($wordNode, 'CONTAINS', Relationship::DirectionOut)
						  ->setMaxDepth(1)
						  ->getSinglePath() ;
		// var_dump($path);
	}

	/**
	 * This function checks if the word node exists in Neo4j DB
	 * @param  String  $word 
	 * @return boolean       
	 */
	public function isWordNodeExists($word){
		if(empty($word)) throw new Exception("Empty word given. Not allowed", 1000);
		
		$wordNode = $this->_wordIndex->find('words',$word );
		return (empty($wordNode)) ? false:$wordNode ;
	}

	/**
	 * This function checks if the email node exists in Neo4j DB
	 * @param  String  $emailToken 
	 * @return boolean             
	 */
	public function isEmailNodeExists($emailToken){
		if(empty($emailToken)) throw new Exception("Empty email token given. Not allowed", 1001);
		
		$emailNode = $this->_emailIndex->find('emails',$emailToken);
		return (empty($emailNode)) ? false:$emailNode ;
	}
}