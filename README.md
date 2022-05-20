# ldl-orchestrator

Compile, load and dump env files and symfony service files.

The symfony DIC container can be dumped to a file to be loaded after or not written at all and loaded 
straight in memory. The latter is convenient for development to avoid having to write files and run into file locking
issues.

Orchestrator is the result of the combination of two packages:

ldl-framework/container-builder + ldl-framework/env-builder

It aims to simplify the work of the two previous packages into one package.

