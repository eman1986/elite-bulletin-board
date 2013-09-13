/*
Filename: bbcode.js
Date Modified: 7/21/08

This File contain the code used for the BBCode prompt.
*/

//bold text function.
function bold(val){ 
var bold_txt = prompt("Enter Text to Bold.",""); 
if (bold_txt == null ){
//do nothing.
}else if (bold_txt == "" || bold_txt == " "){
alert("No text was entered."); 
}else {
var which ="[b]" +bold_txt+ "[/b]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
} 
//italic function
function italic(val){ 
var italic_txt = prompt("Enter Text to italicize.",""); 
if (italic_txt == null ){
//do nothing.
}else if (italic_txt == "" || italic_txt == " "){
alert("No text was entered."); 
}else {
var which ="[i]" +italic_txt+ "[/i]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
} 
//underline function.
function underline(val){ 
var underline_txt = prompt("Enter Text to underline.",""); 
if (underline_txt == null ){
//do nothing.
}else if (underline_txt == "" || underline_txt == " "){
alert("No text was entered."); 
}else {
var which ="[u]" +underline_txt+ "[/u]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
}
//url function.
function url(val){ 
var url_txt = prompt("Enter web link including protocol(ex: http://).",""); 
if (url_txt == null ){
//do nothing.
}else if (url_txt == "" || url_txt == " "){
alert("No text was entered."); 
}else {
var which ="[url]" +url_txt+ "[/url]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
}
//quote function.
function quote(val){ 
var quote_txt = prompt("Enter some text to quote.",""); 
if (quote_txt == null ){
//do nothing.
}else if (quote_txt == "" || quote_txt == " "){
alert("No text was entered."); 
}else {
var which ="[quote]" +quote_txt+ "[/quote]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
}
//code function.
function code(val){ 
var code_txt = prompt("Enter some code.",""); 
if (code_txt == null ){
//do nothing.
}else if (code_txt == "" || code_txt == " "){
alert("No text was entered."); 
}else {
var which ="[code]" +code_txt+ "[/code]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
}
//marque function.
function marque(val){ 
var marque_txt = prompt("Enter some text to marque.",""); 
if (marque_txt == null ){
//do nothing.
}else if (marque_txt == "" || marque_txt == " "){
alert("No text was entered."); 
}else {
var which ="[marque]" +marque_txt+ "[/marque]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
}
//Superscript function.
function sup(val){ 
var sup_txt = prompt("Enter in some text.",""); 
if (sup_txt == null ){
//do nothing.
}else if (sup_txt == "" || sup_txt == " "){
alert("No text was entered."); 
}else {
var which ="[sup]" +sup_txt+ "[/sup]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
}
//Subscript function.
function sub(val){ 
var sub_txt = prompt("Enter in some text.",""); 
if (sub_txt == null ){
//do nothing.
}else if (sub_txt == "" || sub_txt == " "){
alert("No text was entered."); 
}else {
var which ="[sub]" +sub_txt+ "[/sub]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
}
//list function.
function list(val){ 
var list_txt = prompt("Enter in some text.",""); 
if (list_txt == null ){
//do nothing.
}else if (list_txt == "" || list_txt == " "){
alert("No text was entered."); 
}else {
var which ="[list]" +list_txt+ "[/list]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
}
//image function.
function img(val){ 
var img_txt = prompt("Enter link to an image.",""); 
if (img_txt == null ){
//do nothing.
}else if (img_txt == "" || img_txt == " "){
alert("No text was entered."); 
}else {
var which ="[img]" +img_txt+ "[/img]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
}
//left function.
function left(val){ 
var left_txt = prompt("Enter in some text.",""); 
if (left_txt == null ){
//do nothing.
}else if (left_txt == "" || left_txt == " "){
alert("No text was entered."); 
}else {
var which ="[left]" +left_txt+ "[/left]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
}
//center function.
function center(val){ 
var center_txt = prompt("Enter in some text.",""); 
if (center_txt == null ){
//do nothing.
}else if (center_txt == "" || center_txt == " "){
alert("No text was entered."); 
}else {
var which ="[center]" +center_txt+ "[/center]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
}
//right function.
function right(val){ 
var right_txt = prompt("Enter in some text.",""); 
if (right_txt == null ){
//do nothing.
}else if (right_txt == "" || right_txt == " "){
alert("No text was entered."); 
}else {
var which ="[right]" +right_txt+ "[/right]";
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
} 
}
//smile function.
function smile(which, val){
  document.getElementById(""+ val +"").value = document.getElementById(""+ val +"").value + which;
}
