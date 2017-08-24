LOCALROOT=/Applications/MAMP/htdocs/flowrss/
INSTALLSERVER=s602158198.online.de
INSTALLUSER=u83047909

local:
	rsync -aP robot $(LOCALROOT)
	rsync -aP lib $(LOCALROOT)
	rsync -aP app $(LOCALROOT)

install:
	rsync -aP robot $(INSTALLUSER)@$(INSTALLSERVER):flow-rss.com/htdocs/
	rsync -aP lib $(INSTALLUSER)@$(INSTALLSERVER):flow-rss.com/htdocs/
	rsync -aP app $(INSTALLUSER)@$(INSTALLSERVER):flow-rss.com/htdocs/

commit:
	git add ./*
	git commit
	git push -u origin master

update:
	git pull origin master
