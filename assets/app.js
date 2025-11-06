/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './bootstrap.js';
import $ from 'jquery';
(window).$ = $;
(window).jQuery = $;
import select2 from 'select2';
//
select2($);
//
// $(function () {
//     $('select').select2();
// });

