/*!
 * jQuery Event Magic v0.0.3
 * https://github.com/frankfoerster/eventmagic
 *
 * Copyright (c) 2013, Frank Förster (http://frankfoerster.com)
 * Licensed under the MIT License
 */

!function(n){function e(n,e){var f,t,o,c,i,u,a,d,h=e.length;for(n="on"!=n&&"off"!=n?"on":n,f=0;h>f;f++)if(c=e[f][0],"undefined"!=typeof e[f][2])for(i=e[f][1],t=0,o=e[f][2].length;o>t;t++)a=e[f][2][t][0],d=e[f][2][t][1],c[n](a,i,d);else u=e[f][1],c[n](u)}n.extend({attachEvents:function(n){e("on",n)},detachEvents:function(n){e("off",n)}})}(jQuery);