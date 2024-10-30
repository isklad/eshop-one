#!/usr/bin/env python

import os
import re
from jinja2 import Environment, FileSystemLoader


template_file = os.environ.get('TEMPLATE_FILE', 'docker/deploy/docker-compose.j2')
rendered_file = os.environ.get('RENDERED_FILE', 'docker/deploy/docker-compose.yml')
load_github_vars = os.environ.get('LOAD_GITHUB_VARS', 'false')

env = Environment(loader=FileSystemLoader('.'))
template = env.get_template(template_file)

with open(template_file) as f:
  template_content = f.read()

# Find Jinja variables in the template
jinja_vars = re.findall(r'\{\{ *(.*?) *\}\}', template_content)

# Prepare context with environment variables
# These variables are statically defined in the template
context = {}
for var in jinja_vars:
  env_var_value = os.environ.get(var.strip()) or os.environ.get(var.strip().upper()) or os.environ.get(var.strip().lower())
  context[var.strip()] = env_var_value

# Search for environment variables with ENV_ prefix and generate list
# These variables are fetched from github secrets/var store upon workflow run
if load_github_vars == 'true':
  env_list = []
  for key, value in os.environ.items():
    if key.startswith('ENV_'):
      modified_key = key.replace('ENV_', '', 1)
      env_list.append(f"{modified_key}={value}")

  # Add the generated list to the context
  if env_list:
    context['docker_env_vars'] = "\n".join(env_list)

rendered_docker_compose = template.render(context)

with open(rendered_file, 'w') as f:
  f.write(rendered_docker_compose)

print('docker-compose has been generated.')
