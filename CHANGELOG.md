# LDL Orchestrator Changelog

All changes to this project are documented in this file.

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [vx.x.x] - xxxx-xx-xx

### Added

- feature/1202421580425638 - Facade method to build from single files
- feature/1202367657066623 - Add optional "start container" param to OrchestratorCompiler::compile 
- feature/1201943740776108 - Separate OrchestratorBuilder config from main OrchestratorConfig
- feature/1201611356837883 - Integrate ldl-env-builder + ldl-container-builder

### Changed

- fix/1202436361491329 - Default orchestrator loader arguments are incorrect
- fix/1202435910307917 - Invalid usage of variables on OrchestratorLoader::loadDirectory
- fix/1202421899978588 - Typo fix in OrchestratorFacade::fromFiles (plus facade interface)
- fix/1202405405914487 - Rename OrchestratorCompiler to OrchestratorBuilder
- fix/1202368848674808 - Orchestrator not able to resolve %env(VAR)% in services
- fix/1202352397665057 - Make OrchestratorCompiler non-static
- fix/1202313577014118 - Simplify orchestrator
- fix/1201973104924655 - Use ContainerDumpOptionsInterface instead of array
- fix/1201967437941088 - OrchestratorBuilder lacks container dump options

