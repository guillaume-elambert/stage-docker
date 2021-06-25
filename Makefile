.PHONY: start stop run build rebuild clean

start:
	@ ./scripts/start.sh
	@ echo

stop:
	@ ./scripts/stop.sh
	@ echo

run:
	@ ./scripts/run.sh
	@ echo

build:
	@ ./scripts/build.sh
	@ echo

rebuild: clean build
	@ ./scripts/run.sh
	@ echo

clean:
	@ ./scripts/clean.sh
	@ echo