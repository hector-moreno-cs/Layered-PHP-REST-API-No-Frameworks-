class Solution:
    def myAtoi(self, s: str) -> int:
        if len(s) == 0:
            return 0
        newStr = []
        s = s.strip()
        if s[0] == '-':
            newStr.append('-')
            i = 1
            while s[i] == '0':
                i += 1
            for i in range(len(s)):
                if ord(s[i]) > 48 and ord(s[i]) < 58:
                    newStr.append(s[i])
                else:
                    break
        else:
            i = 1
            while s[i] == '0':
                i += 1
            for i in range(len(s)):
                if ord(s[i]) > 48 and ord(s[i]) < 58:
                    newStr.append(s[i])
                else:
                    break
        newInt = ''.join(newStr)
        print(newInt)
        return 1

obj = Solution()

print(obj.myAtoi("-042"))