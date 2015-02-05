var juste 	= "Vous avez raison !";
var faux 	= "Vous avez tort.";

function utf8_decode(str_data) {
  //  discuss at: http://phpjs.org/functions/utf8_decode/
  // original by: Webtoolkit.info (http://www.webtoolkit.info/)
  //    input by: Aman Gupta
  //    input by: Brett Zamir (http://brett-zamir.me)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Norman "zEh" Fuchs
  // bugfixed by: hitwork
  // bugfixed by: Onno Marsman
  // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: kirilloid
  //   example 1: utf8_decode('Kevin van Zonneveld');
  //   returns 1: 'Kevin van Zonneveld'

  var tmp_arr = [],
    i = 0,
    ac = 0,
    c1 = 0,
    c2 = 0,
    c3 = 0,
    c4 = 0;

  str_data += '';

  while (i < str_data.length) {
    c1 = str_data.charCodeAt(i);
    if (c1 <= 191) {
      tmp_arr[ac++] = String.fromCharCode(c1);
      i++;
    } else if (c1 <= 223) {
      c2 = str_data.charCodeAt(i + 1);
      tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
      i += 2;
    } else if (c1 <= 239) {
      // http://en.wikipedia.org/wiki/UTF-8#Codepage_layout
      c2 = str_data.charCodeAt(i + 1);
      c3 = str_data.charCodeAt(i + 2);
      tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
      i += 3;
    } else {
      c2 = str_data.charCodeAt(i + 1);
      c3 = str_data.charCodeAt(i + 2);
      c4 = str_data.charCodeAt(i + 3);
      c1 = ((c1 & 7) << 18) | ((c2 & 63) << 12) | ((c3 & 63) << 6) | (c4 & 63);
      c1 -= 0x10000;
      tmp_arr[ac++] = String.fromCharCode(0xD800 | ((c1 >> 10) & 0x3FF));
      tmp_arr[ac++] = String.fromCharCode(0xDC00 | (c1 & 0x3FF));
      i += 4;
    }
  }

  return tmp_arr.join('');
}

function affichage_bloc_reponse() {
	document.getElementsByClassName('reponse_quiz').item(0).style.display 		= "inline-block";
	document.getElementsByClassName('reponse_quiz').item(0).style.border 		= "solid black 1px";
	document.getElementsByClassName('reponse_quiz').item(0).style.paddingTop 	= "0px";
	document.getElementsByClassName('reponse_quiz').item(0).style.paddingBottom = "0px";
	document.getElementsByClassName('reponse_quiz').item(0).style.paddingLeft 	= "20px";
	document.getElementsByClassName('reponse_quiz').item(0).style.paddingRight	= "20px";
	document.getElementById('choix').style.display = "block";
}

function answer_no(param1,param2){
	var naiss1 = new Date(param1);
	var naiss2 = new Date(param2);
	var formatNaiss1 = naiss1.getDate() + "/" + (naiss1.getMonth() + 1) + "/" + naiss1.getFullYear();
	var formatNaiss2 = naiss2.getDate() + "/" + (naiss2.getMonth() + 1) + "/" + naiss2.getFullYear();
	(naiss1 > naiss2) 	
		? document.getElementsByClassName("reponse_quiz").item(0).innerHTML = juste + ' ' + formatNaiss1 + " contre " + formatNaiss2
		: document.getElementsByClassName("reponse_quiz").item(0).innerHTML = faux  + ' ' + formatNaiss1 + " contre " + formatNaiss2;
		
	affichage_bloc_reponse();
	document.getElementById("answer_no").style.backgroundColor = "blue";
	document.getElementById("answer_no").style.color = "white";
	document.getElementById('answer_no').disabled = "disabled";
	document.getElementById('answer_yes').disabled = "disabled";
	
}

function answer_yes(param1,param2){
	var naiss1 = new Date(param1);
	var naiss2 = new Date(param2);
	var formatNaiss1 = naiss1.getDate() + "/" + (naiss1.getMonth() + 1) + "/" + naiss1.getFullYear();
	var formatNaiss2 = naiss2.getDate() + "/" + (naiss2.getMonth() + 1) + "/" + naiss2.getFullYear();
	(naiss1 < naiss2) 	
		? document.getElementsByClassName("reponse_quiz").item(0).innerHTML = juste + ' ' + formatNaiss1 + " contre " + formatNaiss2
		: document.getElementsByClassName("reponse_quiz").item(0).innerHTML = faux  + ' ' + formatNaiss1 + " contre " + formatNaiss2;
	
	affichage_bloc_reponse();
	document.getElementById("answer_yes").style.backgroundColor = "blue";
	document.getElementById("answer_yes").style.color = "white";
	document.getElementById('answer_no').disabled = 'disabled';
	document.getElementById('answer_yes').disabled = 'disabled';
}

function verif_photo(id,choix_photo,reponse,compteur) {
	reponse = utf8_decode(reponse);
	document.getElementsByClassName('reponse_quiz').item(0).innerHTML = (choix_photo == id) ? juste + " C'est " + reponse 
																							: faux + " C'est " + reponse;
	
	affichage_bloc_reponse();
	var elt = document.getElementById("question").children;
	var i = 0;
	for (i = 0; i < elt.length; ++i) {
		if (i == compteur) {
			elt.item(i).style.backgroundColor = "blue";
			elt.item(i).style.color = "white";
		}
		elt.item(i).disabled = "disabled";
	}
	
}

function verif_commun(id,choix_acteur,nom) {
	nom = utf8_decode(nom);
	document.getElementsByClassName("reponse_quiz").item(0).innerHTML = (choix_acteur == 1) 
		? juste + " La r&eacute;ponse est " +nom
		: faux  + " La r&eacute;ponse est " +nom;
	
	affichage_bloc_reponse();
	var elt = document.getElementById("question").children;
	var i = 0;
	for (i = 0; i < elt.length; ++i) {
		if (i == id) {
			elt.item(i).style.backgroundColor = "blue";
			elt.item(i).style.color = "white";
		}
		elt.item(i).disabled = "disabled";
	}
}

function verif_year_disney(id,oneannee,choixannee){
	var juste 	= "Vous avez raison ! La r&eacute;ponse &eacute;tait : ";
	var faux 	= "Vous avez tort. La r&eacute;ponse &eacute;tait : ";
	document.getElementsByClassName("reponse_quiz").item(0).innerHTML = (oneannee == choixannee) ? juste+oneannee : faux+oneannee;
	
	document.getElementById(oneannee).style.backgroundColor = "red";
	document.getElementById(oneannee).style.color = "yellow";
	
	var elt = document.getElementsByClassName("button_annee");
	var i = 0;
	for(i = 0; i < elt.length; ++i) {
		if (i == id) {
			elt.item(i).style.backgroundColor = "blue";
			elt.item(i).style.color = "white";
		}
		elt.item(i).disabled = "disabled";
	}
	
	affichage_bloc_reponse();
}

function answer_film_no(naiss1,naiss2){

	(naiss1 > naiss2) 	
		? document.getElementsByClassName("reponse_quiz").item(0).innerHTML = juste + ' ' + naiss1 + " contre " + naiss2
		: document.getElementsByClassName("reponse_quiz").item(0).innerHTML = faux  + ' ' + naiss1 + " contre " + naiss2;
	
	affichage_bloc_reponse();
	document.getElementById("answer_no").style.backgroundColor = "blue";
	document.getElementById("answer_no").style.color = "white";
	document.getElementById('answer_no').disabled = "disabled";
	document.getElementById('answer_yes').disabled = "disabled";
}

function answer_film_yes(naiss1,naiss2){

	(naiss1 < naiss2) 	
		? document.getElementsByClassName("reponse_quiz").item(0).innerHTML = juste + ' ' + naiss1 + " contre " + naiss2
		: document.getElementsByClassName("reponse_quiz").item(0).innerHTML = faux  + ' ' + naiss1 + " contre " + naiss2;
	
	affichage_bloc_reponse();
	document.getElementById("answer_yes").style.backgroundColor = "blue";
	document.getElementById("answer_yes").style.color = "white";
	document.getElementById('answer_no').disabled = "disabled";
	document.getElementById('answer_yes').disabled = "disabled";
}

function verif_director(id,choix,answer) {
	answer = utf8_decode(answer);
	(choix == 1)	? document.getElementsByClassName("reponse_quiz").item(0).innerHTML = juste + ' La r&eacute;ponse est ' + answer
					: document.getElementsByClassName("reponse_quiz").item(0).innerHTML = faux  + ' La r&eacute;ponse est ' + answer;
	var elt = document.getElementsByClassName("button_dir");
	document.getElementsByName(answer).item(0).style.backgroundColor = "red";
	document.getElementsByName(answer).item(0).style.color = "yellow";
	
	var i = 0;
	for(i = 0; i < elt.length; ++i) {
		if (i == id) {
			elt.item(i).style.backgroundColor = "blue";
			elt.item(i).style.color = "white";
		}
		elt.item(i).disabled = "disabled";
	}
	
	affichage_bloc_reponse();
}

function again() {
	window.location.reload(5);
}

function changerQuiz() {
	window.location = "./liste_quiz.php";
}

function show_list(id) {
	document.getElementById('montrer_film_'+id).style.display = "block";
	document.getElementById('show_movie_'+id).textContent = "";
}

function show_infos(id) {
	document.getElementById('montrer_infos_'+id).style.display = "block";
	document.getElementById('show_infos_'+id).textContent = "";
}


	
