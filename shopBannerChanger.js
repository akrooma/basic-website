
//Massiiv piltidega.
var pictures = new Array();

//Massiivi elementide väärtustamine.
pictures[0] = "../Legit/img/shopBanner/banner1.png";
pictures[1] = "../Legit/img/shopBanner/banner2.png";
pictures[2] = "../Legit/img/shopBanner/banner3.png";
pictures[3] = "../Legit/img/shopBanner/banner4.png";

//Massiivi viimase elemendi indeks.
var countTo = pictures.length-1;
//Mitmendast pildist alustab muutmist.
var countFrom = 1;
//Määrab kui mitme sekundi tagant pilte vahetatakse.
var howLong = 5;

function setFirstImage() {
	document.getElementById("shopBannerImg").src=pictures[0]
}

//Leiab elemendi ID-ga shopBannerImg ja vahetab selle pildi. Kasvatab countFrom väärtust ühe võrra.
//countFrom resetitakse nulli, kui see on samaväärne massiivi viimase elemendi indeksiga.
function rotater() {
	document.getElementById("shopBannerImg").src=pictures[countFrom];
	
	if (countFrom != countTo) {
   	countFrom++;
	} else {
		countFrom = 0;
	}
	
}

//Lehe laadimisel hakatakse pilte vahetama howLong sekundiliste vahedega.
window.onload=setInterval("rotater()", howLong*1000);