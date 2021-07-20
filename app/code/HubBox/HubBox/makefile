
BASE_DIR := $(shell pwd)
BASE_NAME := $(shell basename `pwd`)

all: zip

clean:
	rm -rf ./.dist

./dist: clean
	mkdir ./.dist
	mkdir ./.dist/HubBox_HubBox

cp: ./dist
	cp -R ./Api ./.dist/HubBox_HubBox
	cp -R ./Block ./.dist/HubBox_HubBox
	cp -R ./Command ./.dist/HubBox_HubBox
	cp -R ./Config ./.dist/HubBox_HubBox
	cp -R ./Controller ./.dist/HubBox_HubBox
	cp -R ./Cron ./.dist/HubBox_HubBox
	cp -R ./CustomerData ./.dist/HubBox_HubBox
	cp -R ./etc ./.dist/HubBox_HubBox
	cp -R ./Helper ./.dist/HubBox_HubBox
	cp -R ./i18n ./.dist/HubBox_HubBox
	cp -R ./Logger ./.dist/HubBox_HubBox
	cp -R ./Model ./.dist/HubBox_HubBox
	cp -R ./Observer ./.dist/HubBox_HubBox
	cp -R ./Plugin ./.dist/HubBox_HubBox
	cp -R ./Setup ./.dist/HubBox_HubBox
	cp -R ./view ./.dist/HubBox_HubBox

	cp -R ./LICENSE.md ./.dist/HubBox_HubBox
	cp -R ./registration.php ./.dist/HubBox_HubBox
	cp -R ./composer.json ./.dist/HubBox_HubBox

zip: cp
	cd .dist; zip -r HubBox_HubBox-`cat ../VERSION`.zip HubBox_HubBox
	cd .dist; cp HubBox_HubBox-`cat ../VERSION`.zip HubBox_HubBox-latest.zip
