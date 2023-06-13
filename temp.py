def string_to_number(s):
    is_negative = False
    if s[0] == '-':
        is_negative = True
        s = s[1:]

    num = 0
    decimal_found = False
    decimal_multiplier = 1

    for char in s:
        if char == '.':
            decimal_found = True
            continue

        if not decimal_found:
            num = num * 10 + ord(char) - ord('0')
        else:
            decimal_multiplier /= 10
            num += (ord(char) - ord('0')) * decimal_multiplier

    if is_negative:
        num = -num

    return num

# Test the function
string_number = "123"
print(string_to_number(string_number))  # Output: 123

string_number = "-123.45"
print(string_to_number(string_number))  # Output: -123.45