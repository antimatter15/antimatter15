"""
This prototype application is available under the terms of GPLv3
Permission for other licences can and probably will be granted
if emailed at antimatter15@gmail.com.
"""

import httplib
import urllib
import Cookie
import urlparse
import re
import pickle

from optparse import OptionParser

parser = OptionParser(usage="usage: %prog [options] email password")
parser.add_option("-f", "--file", dest="file",
                  help="save session data to FILE (default state.txt)")
parser.add_option("-v", "--verbose",
                  action="store_true", dest="verbose",help="show login info")
                  
(options, args) = parser.parse_args()

if len(args) != 2:
  parser.error("incorrect number of arguments")
  exit()
else:
  email = args[0]
  password = args[1]
  
conn = httplib.HTTPSConnection("www.google.com") #wave likes being secure
if options.verbose:
  conn.set_debuglevel(8)



#conn.request("GET", url, "",headers)

url = "/accounts/ServiceLoginAuth?service=wave"
conn.request("GET", url, "")

r1 = conn.getresponse()
print "Got Login Token",r1.status, r1.reason, r1.version
#print r1.read()

C = Cookie.SimpleCookie(r1.getheader("Set-Cookie"))



print "Login Token",C["GALX"].value

#url = "/accounts/ServiceLoginAuth?service=wave" #again

conn.close()

headers = {
"Cookie": "GALX="+C["GALX"].value,
"Content-type": "application/x-www-form-urlencoded"
}

params = urllib.urlencode({ #for some odd reason params looks like spam
'Email': email,
'GALX': C["GALX"].value,
'Passwd': password,
'PersistentCookie': "yes",
'asts': '',
'continue': "https://wave.google.com/wave/",
'followup': "https://wave.google.com/wave/",
'ltmpl': "standard",
'nui': 1,
"rmShown": 1,
'service': "wave",
"signIn": "Sign in"
})

conn.request("POST", url, params, headers)

r2 = conn.getresponse()
print "Logged In",r2.status, r2.reason, r2.version
#print r2.read()

C2 = Cookie.SimpleCookie(r2.getheader("Set-Cookie"))

conn.close()

url = "/accounts/CheckCookie?continue=https%3A%2F%2Fwave.google.com%2Fwave%2F&followup=https%3A%2F%2Fwave.google.com%2Fwave%2F&service=wave&ltmpl=standard&chtml=LoginDoneHtml"


cdat = ""
for cookie in C2:
  cdat += cookie + "=" + C2[cookie].value + "; "

headers = {
"Cookie": cdat[:-2]
}


conn.request("GET", url, "", headers)
r3 = conn.getresponse()
print "Checked Cookie",r3.status, r3.reason, r3.version

loc = r3.getheader("Location")
conn.close()

conn = httplib.HTTPSConnection("wave.google.com") #wave likes being secure
if options.verbose:
  conn.set_debuglevel(8)

#us = urlparse.urlsplit(loc)
#subpath = us[2]+"?"+us[3]

#print subpath

conn.request("GET", loc, "")
r1 = conn.getresponse()
print "Authenticated Wave",r1.status, r1.reason, r1.version

C = Cookie.SimpleCookie(r1.getheader("Set-Cookie"))

print "Auth Key",C["WAVE"].value

conn.close()

conn.request("GET", "/wave/", "", {"Cookie": "WAVE="+C["WAVE"].value})
r2 = conn.getresponse()
print "Acquired Session Key",r2.status, r2.reason, r2.version


m = re.compile(r'userProfile:\{id:(.*?),username:').search(r2.read())
session = int(m.group(1).replace("'","")) #THE MAGICL SESSION
print "Profile Session ID",session

#magical cookies are important

conn.close()

state = {
"session": session,
"cookie": C["WAVE"].value
}

saveas = "state.txt"
if options.file:
  saveas = options.file
pickle.dump(state, open(saveas,"w"))
print "Saved Session and Cookie to "+saveas
