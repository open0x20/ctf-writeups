# Google Beginner Quest 2021

## 1 Chemical Plant CCTV (Rev)
The original password check:
```javascript
const checkPassword = () => {
  const v = document.getElementById("password").value;
  const p = Array.from(v).map(a => 0xCafe + a.charCodeAt(0));

  if(p[0] === 52037 &&
     p[6] === 52081 &&
     p[5] === 52063 &&
     p[1] === 52077 &&
     p[9] === 52077 &&
     p[10] === 52080 &&
     p[4] === 52046 &&
     p[3] === 52066 &&
     p[8] === 52085 &&
     p[7] === 52081 &&
     p[2] === 52077 &&
     p[11] === 52066) {
    window.location.replace(v + ".html");
  } else {
    alert("Wrong password!");
  }
}
```

The ASCII representation of each letter of the supplied password will be added with 0xCAFE hexadecimal or 51966 decimal. The resulting array of numbers will then be compared to the one below. Simply substract 51966 from each number and you'll get the ASCII representation for each letter of the password. 

Just open the browser console and manipulate some data.
```javascript
var p = []
p[0] = 52037
p[6] = 52081
p[5] = 52063
p[1] = 52077
p[9] = 52077
p[10] = 52080
p[4] = 52046
p[3] = 52066
p[8] = 52085
p[7] = 52081
p[2] = 52077
p[11] = 52066

var w = p.map(a => a - 0xCafe)

console.log(w)
// Outputs: a list of numbers, letters in ascii representation

var o = w.map(a => String.fromCharCode(a)).toString().replace(/,/g, '')

console.log(o)
// Outputs: GoodPassword
```
Put in `GoodPassword` in the password field and you'll get the flag.

_CTF{IJustHopeThisIsNotOnShodan}_

## 2 Apartment Logic Lock (Misc)
We get a file with a gibberish name. Putting it through the `file` command tells us that it's a zip archive:
```
$ file 419bcccb21e0773e1a7db7ddcb4d557c7d19b5a76cd42
1851d9e20ab451702b252de11e90d14c3992f14bb4c5b330ea53
68f8c52eb1e4c8f82f153aea6566d56

Zip archive data, at least v2.0 to extract
```

Just rename the file to something.zip and use a gui tool to browser the archive. Actually there is just one file in there called `logic-lock.png` with a bunch of connected logic gates. Find the correct input to get a logical true at the output.

- A - 0
- B - 1
- C - 1
- D - 0
- E - 0
- F - 1
- G - 0
- H - 0
- I - 1
- J - 1

CTF{BCFIJ}

## 3 Streets High Speed Chase (Misc)
Just programm the `controlCar` function to dodge the cars. Ezpz.

```javascript
 
    const bestLineMaxDis = 12

    function getStreetObj(scanArray) {
        var streetObj = {
            first: 0,
            second: 0,
            third: 0,
            on: 0,
            leftCount: 0
        }

        let leftCount = 0
        let foundStreet = false

        for (let i = 0; i < scanArray.length; i++) {
            if (scanArray[i] == 0) {
                if (!foundStreet)
                leftCount++
                continue
            }

            if (streetObj.first == 0 || streetObj.first == scanArray[i]) {
                foundStreet = true
                streetObj.first = scanArray[i]
                continue
            }
            if (streetObj.second == 0 || streetObj.second == scanArray[i]) {
                foundStreet = true
                streetObj.second = scanArray[i]
                continue
            }
            if (streetObj.third == 0 || streetObj.third == scanArray[i]) {
                foundStreet = true
                streetObj.third = scanArray[i]
                continue
            }

            console.error("strange value left:" + scanArray[i] + ";\n streetObj: " + streetObj)

        }

        leftCount-- // line 0 is alway 0

        if (leftCount == 0) streetObj.on = 3
        if (leftCount == 3) streetObj.on = 2
        if (leftCount == 6) streetObj.on = 1

        streetObj.leftCount = leftCount

        //überholen auf 3
        if (streetObj.first < 0) {
            streetObj.on = 3
            streetObj.first = 0
            streetObj.second = 0
            streetObj.third = 30 //perfection 
        }

        return streetObj
    }

    function getBestLine(streetObj) {
        if (streetObj.leftCount >= 5) {
            if (streetObj.first < bestLineMaxDis && streetObj.first > 0) return 2
            else return 1
        }

        if (streetObj.leftCount <= 1) {
            if (streetObj.third < bestLineMaxDis && streetObj.third > 0) return 2
            else return 3
        }

        if (streetObj.leftCount >= 2 || streetObj.leftCount <= 4) {
            if (streetObj.second < bestLineMaxDis && streetObj.second > 0) {
                if (streetObj.first > streetObj.third) return 1
                else return 3
            }
            if (streetObj.leftCount >= 3) return 2
            if (streetObj.leftCount <= 3) return 2
        }
    }

    const streetObj = getStreetObj(scanArray)
    let bestLine = getBestLine(streetObj)



    console.log(scanArray)
    console.log(streetObj)
    console.log(bestLine)


    if (bestLine == 1) {
        if (streetObj.on == 1) return 0
        else return -1
    }

    if (bestLine == 3) {
        if (streetObj.on == 3) return 0
        else return 1
    } 

    if (bestLine == 2) {
        if (streetObj.leftCount > 3) return 1
        if (streetObj.leftCount < 3) return -1
    }


```

_CTF{cbe138a2cd7bd97ab726ebd67e3b7126707f3e7f}_
