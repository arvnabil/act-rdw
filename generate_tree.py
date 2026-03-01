import sys

with open('structure.txt', 'r') as f:
    lines = [line.strip() for line in f if line.strip()]

class Node:
    def __init__(self, name):
        self.name = name
        self.children = {}

root = Node('Modules')

for line in lines:
    parts = line.split('/Modules')
    if len(parts) < 2 or not parts[1]:
        continue
    path_parts = [p for p in parts[1].split('/') if p]
    
    current = root
    for p in path_parts:
        if p not in current.children:
            current.children[p] = Node(p)
        current = current.children[p]

def print_tree(node, prefix=''):
    res = ''
    children = list(node.children.values())
    for i, child in enumerate(children):
        is_last = (i == len(children) - 1)
        res += prefix + ('└── ' if is_last else '├── ') + child.name + '\n'
        res += print_tree(child, prefix + ('    ' if is_last else '│   '))
    return res

tree_str = f"```text\nModules\n{print_tree(root)}```\n"

with open('module_structure.md', 'w') as f:
    f.write('# Struktur File & Folder Modul\n\nBerikut adalah struktur folder dan file lengkap dari direktori `Modules/` dalam arsitektur proyek ini:\n\n')
    f.write(tree_str)
