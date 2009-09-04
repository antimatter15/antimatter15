SHA1 = function() {
  // function 'f' [§4.1.1]
  var f = function(s, x, y, z) {
      switch (s) {
          case 0: return (x & y) ^ (~x & z);           // Ch()
          case 1: return x ^ y ^ z;                    // Parity()
          case 2: return (x & y) ^ (x & z) ^ (y & z);  // Maj()
          case 3: return x ^ y ^ z;                    // Parity()
      }
  }, ROTL = function(x, n) {
      return (x<<n) | (x>>>(32-n));
  },toHexStr = function(e){
        var s = '', v, i;
        for(i = 7; i >= 0; i--) {
            v = (e >>> (i * 4)) & 0xf;
            s += v.toString(16);
        }
        return s;
    }
  return {
    hash : function(msg) {
      // constants [§4.2.1]
      msg += String.fromCharCode(0x80); // add trailing '1' bit to string [§5.1.1]
      
      var K = [0x5a827999, 0x6ed9eba1, 0x8f1bbcdc, 0xca62c1d6],
      l = Math.ceil(msg.length/4) + 2,  // long enough to contain msg plus 2-word length
      N = Math.ceil(l/16),              // in N 16-int blocks
      M = new Array(N),
      i=0,
      H0 = 0x67452301,
      H1 = 0xefcdab89,
      H2 = 0x98badcfe,
      H3 = 0x10325476,
      H4 = 0xc3d2e1f0,
      W = new Array(80),
      a, b, c, d, e,
      t, j;
      // PREPROCESSING 
   

  
      // convert string msg into 512-bit/16-integer blocks arrays of ints [§5.2.1]

      for (; i<N; i++) {
          M[i] = new Array(16);
          for (j=0; j<16; j++) {  // encode 4 chars per integer, big-endian encoding
              M[i][j] = (msg.charCodeAt(i*64+j*4)<<24) | (msg.charCodeAt(i*64+j*4+1)<<16) | 
                        (msg.charCodeAt(i*64+j*4+2)<<8) | (msg.charCodeAt(i*64+j*4+3));
          }
      }
      // add length (in bits) into final pair of 32-bit integers (big-endian) [5.1.1]
      // note: most significant word would be ((len-1)*8 >>> 32, but since JS converts
      // bitwise-op args to 32 bits, we need to simulate this by arithmetic operators
      M[N-1][14] = ((msg.length-1)*8) / Math.pow(2, 32); M[N-1][14] = Math.floor(M[N-1][14])
      M[N-1][15] = ((msg.length-1)*8) & 0xffffffff;
  
      // set initial hash value [§5.3.1]

  
      // HASH COMPUTATION [§6.1.2]
  

      for (i=0; i<N; i++) {
  
          // 1 - prepare message schedule 'W'
          for (t=0;  t<16; t++) W[t] = M[i][t];
          for (t=16; t<80; t++) W[t] = ROTL(W[t-3] ^ W[t-8] ^ W[t-14] ^ W[t-16], 1);
  
          // 2 - initialise five working variables a, b, c, d, e with previous hash value
          a = H0; b = H1; c = H2; d = H3; e = H4;
  
          // 3 - main loop
          for (t=0; t<80; t++) {
              var s = Math.floor(t/20), // seq for blocks of 'f' functions and 'K' constants
              T = (ROTL(a,5) + f(s,b,c,d) + e + K[s] + W[t]) & 0xffffffff;
              e = d;
              d = c;
              c = ROTL(b, 30);
              b = a;
              a = T;
          }
  
          // 4 - compute the new intermediate hash value
          H0 = (H0+a) & 0xffffffff;  // note 'addition modulo 2^32'
          H1 = (H1+b) & 0xffffffff; 
          H2 = (H2+c) & 0xffffffff; 
          H3 = (H3+d) & 0xffffffff; 
          H4 = (H4+e) & 0xffffffff;
      }
  
      return toHexStr(H0) + toHexStr(H1) + toHexStr(H2) + toHexStr(H3) + toHexStr(H4);
    }
  }
  
}();

