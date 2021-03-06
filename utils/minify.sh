DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd ${DIR}
mkdir -p ../www/css
java -jar ../../yuicompressor-2.4.2.jar --type css --charset utf-8 -o ../www/css/reset.css ../css/reset.css
mkdir -p ../www/css/templates/default
java -jar ../../yuicompressor-2.4.2.jar --type css --charset utf-8 -o ../www/css/templates/default/layout.css ../css/templates/default/layout.css
java -jar ../../yuicompressor-2.4.2.jar --type css --charset utf-8 -o ../www/css/templates/default/colors.css ../css/templates/default/colors.css

cat ../www/css/reset.css ../www/css/templates/default/layout.css ../www/css/templates/default/colors.css > ../www/css/templates/default/style.css
rm -f ../www/css/reset.css ../www/css/templates/default/layout.css ../www/css/templates/default/colors.css
if [ -f ../../7za.exe ];
then
	if [ -f ../www/css/templates/default/style.css.gz ];
		then rm -f ../www/css/templates/default/style.css.gz
	fi;
	../../7za.exe a -tgzip ../www/css/templates/default/style.css.gz ../www/css/templates/default/style.css -mx=9 -mfb=258 -mpass=15
else
	gzip -cf --best ../www/css/templates/default/style.css > ../www/css/templates/default/style.css.gz
fi;
