<?php
/**
 *	CaracoleInflector
 *	This class is an extension of the cake Inflector class.
 *	It adds methods and configuration to deal mostly with accented characters
  **/
class CaracoleInflector extends Object {

	/**
	 *	init
	 *	Init some inflector rules
	 **/
	function init() {
		Inflector::rules('transliteration', array(
			'/œ/' => 'oe',
			'/Œ/' => 'OE',
			'/µ/' => 'u',
			'/€/' => 'euro',
			'/£/' => 'pound',
			'/¥/' => 'yen',
			'/©/' => 'copyright',
			'/®/' => 'registered',
			'/™/' => 'trademark',
		));
	}

	/**
	 *	slug
	 *	We extends the cake Inflector::slug to
	 *		- strip out the most common words used in the current language
	 *		- use - as a replacement
	 *		- put it on lowercase
	 **/
	function slug($string, $language = null) {
		return CaracoleInflector::cleanSlug(strtolower(Inflector::slug($string, '-')), $language);
	}

	/**
	 *	cleanSlug
	 *	Remove all useless words from a slug, like 'you', 'it', 'how', etc
	 *	A language can be passed as the second argument. Current language used as default
	 **/
	function cleanSlug($slug, $language = null) {
		// Getting all words
		$words = array_unique(explode('-', strtolower($slug)));
		// Getting language
		if (empty($language)) $language = Configure::read('Config.language');

		// Getting list of useless words based on language
		switch($language) {
			// English
			case 'eng':
				$uselessWords = array(
				"a", "able", "about", "above", "abroad", "according", "accordingly", "across", "actually", "adj",
				"after", "afterwards", "again", "against", "ago", "ahead", "ain", "all", "allow", "allows", "almost",
				"alone", "along", "alongside", "already", "also", "although", "always", "am", "amid", "amidst", "among",
				"amongst", "an", "and", "another", "any", "anybody", "anyhow", "anyone", "anything", "anyway",
				"anyways", "anywhere", "apart", "appear", "appreciate", "appropriate", "are", "aren", "around", "as",
				"aside", "ask", "asking", "associated", "at", "available", "away", "awfully",
				"b", "back", "backward", "backwards", "be", "became", "because", "become", "becomes", "becoming",
				"been", "before", "beforehand",	"begin", "behind", "being", "believe", "below", "beside", "besides",
				"best", "better", "between", "beyond", "both", "brief", "but", "by",
				"c", "came", "can", "cannot", "cant", "caption", "cause", "causes", "certain", "certainly", "changes",
				"clearly", "mon", "co", "com", "come", "comes", "concerning", "consequently", "consider", "considering",
				"contain", "containing", "contains", "corresponding", "could", "couldn", "course", "currently",
				"d", "dare", "daren", "definitely", "described", "despite", "did", "didn", "different", "directly",
				"do", "does", "doesn", "doing", "done", "don", "down", "downwards", "during",
				"e", "each", "edu", "eg", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough",
				"entirely", "especially", "et", "etc", "even", "ever", "evermore", "every", "everybody", "everyone",
				"everything", "everywhere", "ex", "exactly", "example", "except",
				"f", "fairly", "far", "farther", "few", "fewer", "fifth", "first", "five", "followed", "following",
				"follows", "for", "forever", "former", "formerly", "forth", "forward", "found", "four", "from",
				"further", "furthermore",
				"g", "get", "gets", "getting", "given", "gives", "go", "goes", "going", "gone", "got", "gotten",
				"greetings",
				"h", "had", "hadn", "half", "happens", "hardly", "has", "hasn", "have", "haven", "having", "he",
				"hello", "help", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself",
				"hi", "him", "himself", "his", "hither", "hopefully", "how", "howbeit", "however", "hundred",
				"i", "ie", "if", "ignored", "immediate", "in", "inasmuch", "inc", "indeed", "indicate", "indicated",
				"indicates", "inner", "inside", "insofar", "instead", "into", "inward", "is", "isn", "it", "its",
				"itself",
				"j", "just",
				"k", "keep", "keeps", "kept", "know", "known", "knows",
				"l", "ll", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "like",
				"liked", "likely", "likewise", "little", "look", "looking", "looks", "low", "lower", "ltd",
				"m", "made", "mainly", "make", "makes", "many", "may", "maybe", "mayn", "me", "mean", "meantime",
				"meanwhile", "merely", "might", "mightn", "mine", "minus", "miss", "more", "moreover", "most", "mostly",
				"mr", "mrs", "much", "must", "mustn", "my", "myself",
				"n", "name", "namely", "nd", "near", "nearly", "necessary", "need", "needn", "needs", "neither",
				"never", "neverless", "nevertheless", "new", "next", "nine", "ninety", "no", "nobody", "non", "none",
				"nonetheless", "noone", "nor", "normally", "not", "nothing", "notwithstanding", "novel", "now",
				"nowhere",
				"o", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "on", "once", "one", "ones", "only",
				"onto", "opposite", "or", "other", "others", "otherwise", "ought", "oughtn", "our", "ours", "ourselves",
				"out", "outside", "over", "overall", "own",
				"p", "particular", "particularly", "past", "per", "perhaps", "placed", "please", "plus", "possible",
				"presumably", "probably", "provided", "provides",
				"q", "que", "quite", "qv",
				"r", "re", "rather", "rd", "re", "really", "reasonably", "recent", "recently", "regarding",
				"regardless", "regards", "relatively", "respectively", "right", "round",
				"s", "said", "same", "saw", "say", "saying", "says", "second", "secondly", "see", "seeing", "seem",
				"seemed", "seeming", "seems", "seen", "self", "selves", "sensible", "sent", "serious", "seriously",
				"seven", "several", "shall", "shan", "she", "should", "shouldn", "since", "six", "so", "some",
				"somebody", "someday", "somehow", "someone", "something", "sometime", "sometimes", "somewhat",
				"somewhere", "soon", "sorry", "specified", "specify", "specifying", "still", "sub", "such", "sup",
				"sure",
				"t", "take", "taken", "taking", "tell", "tends", "th", "than", "thank", "thanks", "thanx", "thats",
				"that", "the", "their", "theirs", "them", "themselves", "then", "thence", "there", "thereafter",
				"thereby", "therefore", "therein", "theres", "thereupon", "these", "they", "thing", "things", "think",
				"third", "thirty", "this", "thorough", "thoroughly", "those", "though", "three", "through",
				"throughout", "thru", "thus", "till", "to", "together", "too", "took", "toward", "towards", "tried",
				"tries", "truly", "try", "trying", "twice", "two",
				"u", "un", "under", "underneath", "undoing", "unfortunately", "unless", "unlike", "unlikely", "until",
				"unto", 	"up", "upon", "upwards", "us", "use", "used", "useful", "uses", "using", "usually",
				"v", "value", "various", "versus", "very", "via", "viz", "vs",
				"w", "want", "wants", "was", "wasn", "way", "welcome", "well", "went", "were", "we", "weren", "what",
				"whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein",
				"whereupon", "wherever", "whether", "which", "whichever", "while", "whilst", "whither", "who",
				"whoever", "whole", "whom", "whomever", "whose", "why", "will", "willing", "wish", "with", "within",
				"without", "wonder", "won", "would", "wouldn",
				"x",
				"y", "yes", "yet", "you", "your", "yours", "yourself", "yourselves",
				"z", "zero"
			);
			break;
			// French
			case 'fre':
				$uselessWords = array(
				    'a', 'adieu', 'afin', 'ah', 'ai', 'aie', 'aient', 'aies', 'aille', 'ainsi', 'ait', 'all', 'alla',
					'allais', 'allait', 'allant', 'alle', 'aller', 'allerent', 'allez', 'allons', 'alors', 'apres',
					'apres', 'as', 'assez', 'au', 'aucun', 'aucune', 'aucunes', 'aucuns', 'aupres', 'aupres', 'auquel',
					'aura', 'aurai', 'aurais', 'aurez', 'auront', 'aussi', 'aussitot', 'autant', 'autour', 'autre',
					'autres', 'autrui', 'aux', 'auxquelles', 'auxquels', 'av', 'avaient', 'avais', 'avait', 'aval',
					'avant', 'avec', 'avez', 'avoir', 'avons', 'ayant', 'ayez', 'ayons',
					'bah', 'bas', 'beaucoup', 'bien', 'bonte', 'bout', 'but',
					'c', 'ca', 'car', 'ce', 'ceci', 'cela', 'celle', 'celles', 'celui', 'cependant', 'ces', 'cet',
					'cette', 'ceux', 'chacun', 'chacune', 'chaque', 'chez', 'chut', 'ci', 'circa', 'combien', 'comme',
					'comment', 'commme', 'compte', 'contre', 'crac', 'crainte', 'cote',
					'd', 'dans', 'de', 'deca', 'dedans', 'dehors', 'dela', 'depuis', 'des', 'desquelles', 'desquels',
					'dessous', 'dessus', 'devant', 'deça', 'dire', 'divers', 'diverses', 'donc', 'dont', 'du', 'duquel',
					'durant', 'des', 'depens', 'depit',
					'e', 'elle', 'elles', 'en', 'entre', 'envers', 'es', 'est', 'et', 'etaient', 'etais', 'etait',
					'etant', 'ete', 'etes', 'etiez', 'etions', 'etre', 'eu', 'eurent', 'eut', 'eux',
					'fai', 'faire', 'fais', 'faisais', 'faisait', 'faisant', 'faisons', 'fait', 'faites', 'fasse',
					'faute', 'fera', 'ferai', 'ferais', 'feras', 'ferez', 'ferons', 'firent', 'fit', 'font', 'furent',
					'fut',
					'he', 'helas', 'hola', 'hors', 'he', 'helas',
					'il', 'ils', 'irai', 'irais', 'iras', 'irons', 'iront',
					'j', 'je', 'jusqu', 'jusque',
					'l', 'la', 'laquelle', 'le', 'lequel', 'les', 'lesquelles', 'lesquels', 'leur', 'leurs', 'lieu',
					'loin', 'lors', 'lorsqu', 'lorsque', 'lui',
					'm', 'ma', 'mains', 'maintes', 'maints', 'mais', 'malgre', 'malgre', 'me', 'merci', 'mes', 'mien',
					'mienne', 'miennes', 'miens', 'milieu', 'moi', 'moins', 'mon', 'moyen', 'meme', 'memes',
					'na', 'ne',	'neanmoins', 'ni', 'non', 'nos', 'notre', 'notres', 'nous', 'neanmoins', 'notre',
					'notres',
					'on', 'ont', 'or', 'ou', 'ouais', 'ou',
					'par', 'parce', 'parmi', 'part', 'partant', 'partir', 'partout', 'pas', 'passe', 'passe', 'pendant',
					'personne', 'peu', 'plein', 'plupart', 'plus', 'plusieurs', 'plutot', 'plutot', 'pour', 'pourquoi',
					'pourvu', 'pres', 'prises', 'proche', 'proie', 'pres', 'puis', 'puisqu', 'puisque', 'periode',
					'qu', 'quand', 'que', 'quel', 'quelconque', 'quelle', 'quelles', 'quelqu', 'quelque', 'quelques',
					'quels', 'qui', 'quiconque', 'quoi', 'quoique',
					'revoici', 'revoila', 'revoila', 'rien',
					's', 'sa', 'sais', 'sans', 'sauf', 'se', 'sein', 'selon', 'sens', 'sera', 'serai', 'serais', 'seras',
					'serez', 'serons', 'seront', 'ses', 'si', 'sien', 'sienne', 'siennes', 'siens', 'signe', 'sinon',
					'soi', 'soient', 'sois', 'soit', 'sommes', 'son', 'sont', 'souci', 'sous', 'soyez', 'soyons',
					'suis', 'sur', 'surtout', 'sus',
					'ta', 'tandis', 'tant', 'te', 'tel', 'telle', 'telles', 'tels', 'tes', 'toc', 'toi', 'ton', 'tous',
					'tout', 'toute', 'toutes', 'travers', 'trop', 'treve', 'tu',
					'un', 'une', 'unes', 'uns',
					'va', 'vais', 'vas', 'vers', 'voici', 'voie', 'voila', 'voila', 'vont', 'vos', 'votre', 'votres',
					'vous', 'vu', 'vue', 'votre', 'votres',
					'y',
				);
			break;
			// Default
			default:
				$uselessWords = array();
			break;
		}

		// Removing useless ones
		$words = array_diff($words, $uselessWords);

		// Returning slug
		return implode('-', $words);
	}


	/**
	 *	cleanNonWebCharacters
	 *	Will remove any no-web characters. Such characters include fancy quotes
	 **/
	function cleanNonWebCharacters($str) {
		// Replacement arrays
		return str_replace(
			array('’'),
			array("'"),
			$str
		);;
	}

}
?>