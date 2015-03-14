# Introduction #

This page provides all of the software used to develop eContriver, and explains how to setup the tools for development.

If you are interested in developing using PHP or helping with this project, then I would recommend this setup.


# Software #

Eclipse PHP IDE:
http://www.zend.com/en/community/pdt

Eclipse Subversive:
http://www.eclipse.org/subversive/downloads.php

Slik SVN Ecexutables:
http://www.sliksvn.com/en/download/

Tortoise SVN Context SVN Operations:
http://tortoisesvn.net/downloads

Zend PHP Engine:
http://www.zend.com/en/downloads/

Aptana plugin for Eclipse [FTP](FTP.md) (Use in Help):
http://download.aptana.org/tools/studio/plugin/install/studio

Eclipse SQL Explorer [DB](Visualize.md) (Use in Help):
http://eclipsesql.sourceforge.net/

MySQL Driver for Eclipse SQL Explorer to work:
  * Go to: http://dev.mysql.com/downloads
    * Click on MySQL Connectors
    * Connector/J : http://dev.mysql.com/downloads/connector/j/
  * I cannot get the zip archives to work, so get the tar.gz
  * Extract that archive
  * Move the contents of this archive to the dropins directory
  * Such that the .../mysql-connector-java-x.x.xx directory is now in:
> > .../ecplise/dropins/mysql-connector-java-x.x.xx

  * Open eclipse and open the SQL Explorer Perspective
  * Click on Create Net Connection Profile
  * Give it some arbitrary name
  * Click Add/Edit Drivers and select MySwl Driver, click on Edit
  * Click on the Extra Class Path tab and click on Add JARs...
  * Navigate to dropins and the 'mysql-connector-java-x.x.xx' directory and select the jar
  * Click Open




FileZilla FTP Tool:
http://filezilla-project.org/