#!/usr/bin/env python

import os
import re
from jinja2 import Environment, FileSystemLoader


template_file = os.environ.get('TEMPLATE_FILE', 'docker/deploy/docker-compose.j2')
rendered_file = os.environ.get('RENDERED_FILE', 'docker/deploy/docker-compose.yml')

env = Environment(loader=FileSystemLoader('.'))
template = env.get_template(template_file)

with open(template_file) as f:
  template_content = f.read()

jinja_vars = re.findall(r'\{\{ *(.*?) *\}\}', template_content)

context = {}
for var in jinja_vars:
  env_var_value = os.environ.get(var.strip()) or os.environ.get(var.strip().upper()) or os.environ.get(var.strip().lower())
  context[var.strip()] = env_var_value

rendered_docker_compose = template.render(context)

with open(rendered_file, 'w') as f:
  f.write(rendered_docker_compose)

print('docker-compose.yml has been generated.')