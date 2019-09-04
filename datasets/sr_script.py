import csv
import re
from tempfile import mkstemp
from shutil import move
from os import fdopen, remove

# From https://stackoverflow.com/questions/39086/search-and-replace-a-line-in-a-file-in-python
def replace(file_path, pattern, subst):
    #Create temp file
    fh, abs_path = mkstemp()
    with fdopen(fh,'w') as new_file:
        with open(file_path) as old_file:
            for line in old_file:
                new_file.write(line.replace(pattern, subst))
    #Remove original file
    remove(file_path)
    #Move new file
    move(abs_path, file_path)

# Files for the standard reference database
desc_file = "SR-Leg_ASC/FOOD_DES.txt"
nutrients_file = "SR-Leg_ASC/NUT_DATA.txt"
fdgrps_file = "SR-Leg_ASC/FD_GROUP.txt"
weights_file = "SR-Leg_ASC/WEIGHT.txt"
results = "sr.csv"

items = {}
groups = {}

def remove_char(line):
    return re.sub('~', '', line)

def process(string):
    return re.sub('"','',string)

def process_row(list):
    for i in range(len(list)):
        list[i] = remove_char(list[i])
    return list

with open(fdgrps_file, newline='') as f:
    csvreader = csv.reader(f, delimiter = "^")
    for row in csvreader:
        groups[int(remove_char(row[0]))] = remove_char(process(row[1]))

with open(desc_file, newline = '',encoding = "ISO-8859-1") as f:
    csvreader = csv.reader(f, delimiter = "^")
    num_items = 0
    
    for row in csvreader:
        try:
            num_items += 1
            items[int(remove_char(row[0]))] = [int(remove_char(row[0])),remove_char(str(row[2])),groups[int(remove_char(row[1]))]]
        except:
            print("Error!")

"""
    The values are for the quantities found in 100gms or 100 ml
    calories, carbs, sugars, dietary fiber, soluble fiber, inSoluble fiber, protein, total_fat, sodium, cholestrol,
    vitaminA,vitaminB6,vitaminB12,vitaminC,vitaminD,vitaminE,niacin,thiamin,calcium,iron,magnesium
    phosphorus,potassium,riboflavin,zinc
"""

nutrients = {}
pattern = [208,205,269,291,295,297,203,204,307,601,318,415,418,401,324,340,406,404,301,303,304,305,306,405,309]

with open(nutrients_file,newline = '') as f:
    csvreader = csv.reader(f, delimiter = "^")
    num_items = 0

    for i, line in enumerate(csvreader):
        line = process_row(line)
        if(int(line[0]) not in nutrients):
            nutrients[int(line[0])] = [0]*25
        try:
            if(int(line[1]) in pattern):
                nutrients[int(line[0])][pattern.index(int(line[1]))] = float(line[2])
        
        except Exception as e:
            print(line)
            print(str(e))
            pass

weights = {}
with open(weights_file,newline = '') as f:
    csvreader = csv.reader(f, delimiter = "^")
    num_items = 0
    
    for i, line in enumerate(csvreader):
        line = process_row(line)
        try:
            if(float(line[2]) == 0):
                line[2] = 1
            if int(line[0]) not in weights or int(line[1]) == 1:
                weights[int(line[0])] = [float(line[4])/(100*float(line[2])),str(line[3])]
            if "cup" in str(line[3]):
                weights[int(line[0])] = [float(line[4])/(100*float(line[2])),str(line[3])]
            elif "fl oz" in str(line[3]):
                weights[int(line[0])] = [float(line[4])/(100*float(line[2])),str(line[3])]
            else:
                 continue
        except:
            print(line)

the_set = set()
with open(results, "w") as output:
    writer = csv.writer(output, delimiter ='~',lineterminator='\n')
    for each_num in nutrients.keys():
        line = items[each_num]
        if(line[0] in weights):
             line += weights[line[0]]
        else:
             line += ["1","g"]
        line += nutrients[each_num]
        the_set.add(len(line))
        writer.writerow(line)

print(the_set)
print("Num records: " + str(len(nutrients)))

replace(results,"\"","")

schema = "(foodID,name,food_group,multiplier,quantity_units,calories,total_carbs,sugars,dietary_fiber,soluble_fiber,insoluble_fiber,protein,total_fat,sodium,cholesterol,vitaminA,vitaminB6,vitaminB12,vitaminC,vitaminD,vitaminE,niacin,thiamin,calcium,iron,magnesium,phosphorus,potassium,riboflavin,zinc)"
schema_format = "(%u,%s,%s,%f,%s,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f)"

