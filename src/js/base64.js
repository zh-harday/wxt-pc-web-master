

  //base64编码解码代码
  //编码
    //value = base64encode(utf16to8(src))
      
    //解码
    //value = utf8to16(base64decode(src))
  var base64 = function(){};


  base64.base64encode = function(r) {
    var e, a, o, h, c, t;
    for (o = r.length, a = 0, e = ""; o > a;) {
      if (h = 255 & r.charCodeAt(a++), a == o) {
        e += base64EncodeChars.charAt(h >> 2), e += base64EncodeChars.charAt((3 & h) << 4), e += "==";
        break
      }
      if (c = r.charCodeAt(a++), a == o) {
        e += base64EncodeChars.charAt(h >> 2), e += base64EncodeChars.charAt((3 & h) << 4 | (240 & c) >> 4), e += base64EncodeChars.charAt((15 & c) << 2), e += "=";
        break
      }
      t = r.charCodeAt(a++), e += base64EncodeChars.charAt(h >> 2), e += base64EncodeChars.charAt((3 & h) << 4 | (240 & c) >> 4), e += base64EncodeChars.charAt((15 & c) << 2 | (192 & t) >> 6), e += base64EncodeChars.charAt(63 & t)
    }
    return e
  }
  
  base64.base64decode = function(r) {
    var e, a, o, h, c, t, C;
    for (t = r.length, c = 0, C = ""; t > c;) {
      do e = base64DecodeChars[255 & r.charCodeAt(c++)]; while (t > c && -1 == e);
      if (-1 == e) break;
      do a = base64DecodeChars[255 & r.charCodeAt(c++)]; while (t > c && -1 == a);
      if (-1 == a) break;
      C += String.fromCharCode(e << 2 | (48 & a) >> 4);
      do {
        if (o = 255 & r.charCodeAt(c++), 61 == o) return C;
        o = base64DecodeChars[o]
      } while (t > c && -1 == o);
      if (-1 == o) break;
      C += String.fromCharCode((15 & a) << 4 | (60 & o) >> 2);
      do {
        if (h = 255 & r.charCodeAt(c++), 61 == h) return C;
        h = base64DecodeChars[h]
      } while (t > c && -1 == h);
      if (-1 == h) break;
      C += String.fromCharCode((3 & o) << 6 | h)
    }
    return C
  }
  
  base64.utf16to8 = function(r) {
    var e, a, o, h;
    for (e = "", o = r.length, a = 0; o > a; a++) h = r.charCodeAt(a), h >= 1 && 127 >= h ? e += r.charAt(a) : h > 2047 ? (e += String.fromCharCode(224 | h >> 12 & 15), e += String.fromCharCode(128 | h >> 6 & 63), e += String.fromCharCode(128 | h >> 0 & 63)) : (e += String.fromCharCode(192 | h >> 6 & 31), e += String.fromCharCode(128 | h >> 0 & 63));
    return e
  }
  
  base64.utf8to16 = function(r) {
    var e, a, o, h, c, t;
    for (e = "", o = r.length, a = 0; o > a;) switch (h = r.charCodeAt(a++), h >> 4) {
      case 0:
      case 1:
      case 2:
      case 3:
      case 4:
      case 5:
      case 6:
      case 7:
        e += r.charAt(a - 1);
        break;
      case 12:
      case 13:
        c = r.charCodeAt(a++), e += String.fromCharCode((31 & h) << 6 | 63 & c);
        break;
      case 14:
        c = r.charCodeAt(a++), t = r.charCodeAt(a++), e += String.fromCharCode((15 & h) << 12 | (63 & c) << 6 | (63 & t) << 0)
    }
    return e
  }
  var base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
    base64DecodeChars = new Array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1);


module.exports = base64;

