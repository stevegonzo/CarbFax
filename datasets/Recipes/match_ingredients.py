import csv
import re
import string
import itertools
import nltk
import unidecode

recipe_file = "Data/ingredients.csv"
ingredients_file = "Data/bp.csv"
output_file = "Data/matched_ingredients.csv"
raw_ingredients = {}
stopwords = ["pieces","leaves","organic","tbsp","ml","medium","small","large","all","purpose","halves","thighs","style","inch","shoulder","g","hulled","low","into","mg","washed","extra","wide","cooked","freshly","fresh","low-sodium","cooked","legs","pods","thighs","seeded","diced"]

stopwords.sort(key = len)
stopwords = stopwords[::-1]

def process_words(list):
    for i in range(len(list)):
        curr_word = list[i].lower()
        new_word = ""
        if curr_word[len(curr_word)-1] == 's':
            new_word = curr_word[0:len(curr_word)-1]
        else:
            new_word = curr_word + 's'
        list.append(new_word)
    return list

def find_matches(ingredient):

    copy = ingredient
    ingredient = unidecode.unidecode(ingredient.lower())
    ingredient = ingredient.translate(str.maketrans('', '', string.punctuation))
    ingredient = ".*".join(ingredient.split())
    ingredient = ".*" + ingredient + ".*"

    r = re.compile(ingredient)
    matches = list(filter(r.match, raw_ingredients.keys()))
    
    if(len(matches) == 0):
        
        ingredient = unidecode.unidecode(copy.lower())
        ingredient = ingredient.translate(str.maketrans(string.digits,' '*len(string.digits)))
        ingredient = ingredient.translate(str.maketrans(string.punctuation,' '*len(string.punctuation)))
        
        for i in range(2,5):
            ingredient = ingredient.replace(' '*i, ' ')
        ingredient.strip()
        ingredient = " " + ingredient + " "
        
        for word in stopwords:
            word = " " + word + " "
            ingredient = ingredient.replace(word,' ')

        ingredient = ".*".join(ingredient.split())
        ingredient = ".*" + ingredient + ".*"
    
        r = re.compile(ingredient)
        matches = list(filter(r.match, raw_ingredients.keys()))
        matches.sort(key=len)


    return matches


with open(ingredients_file, "r") as f:
    ingredients_reader = csv.reader(f, delimiter="~")
    for ingredient in ingredients_reader:
        raw_ingredients[ingredient[1].lower()] = ingredient[0]

with open(recipe_file) as f1,open(output_file, "w") as output:
    recipe_reader = csv.reader(f1, delimiter="~")
    writer = csv.writer(output, delimiter="~", lineterminator='\n')
    count = 0
    missed = 0
    exact = 0
    for ingredient in recipe_reader:

        matches = find_matches(ingredient[1])
        matches.sort(key=len)
        
        if(len(matches)):
            #print(ingredient[1])
            #print(matches[0])
            count += 1
            row = [ingredient[0],raw_ingredients[matches[0]],ingredient[2],ingredient[3]]
            writer.writerow(row)

        else:
            #print(ingredient[1])
            missed += 1

print(count)
print(missed)
print(exact)
