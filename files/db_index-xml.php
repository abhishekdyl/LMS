<XMLDB xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" PATH="blocks/galgano_userwork_reporting/db" VERSION="20221129" COMMENT="XMLDB file for Moodle mod/forum" xsi:noNamespaceSchemaLocation="../../../lib/xmldb/xmldb.xsd">
<TABLES>
    <TABLE NAME="userwork_reporting" COMMENT="userwork_reporting table">
        <FIELDS>
        <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="true"/>
        <FIELD NAME="user_id" TYPE="int" LENGTH="10" NOTNULL="true" DEFAULT="0" SEQUENCE="false"/>
        <FIELD NAME="group_name" TYPE="char" LENGTH="220" NOTNULL="false" DEFAULT="0" SEQUENCE="false"/>
        <FIELD NAME="month" TYPE="int" LENGTH="10" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="year" TYPE="int" LENGTH="10" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="timecreated" TYPE="char" LENGTH="220" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="timeupdated" TYPE="char" LENGTH="220" NOTNULL="false" SEQUENCE="false"/>
        </FIELDS>
        <KEYS>
        <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
        </KEYS>
    </TABLE>
    <TABLE NAME="userwork_report_data" COMMENT="userwork_report_data table">
        <FIELDS>
        <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="true"/>
        <FIELD NAME="user_id" TYPE="int" LENGTH="10" NOTNULL="true" DEFAULT="0" SEQUENCE="false"/>
        <FIELD NAME="userwork_report_id" TYPE="int" LENGTH="10" NOTNULL="false" DEFAULT="0" SEQUENCE="false"/>
        <FIELD NAME="monthday" TYPE="int" LENGTH="10" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="time_in" TYPE="char" LENGTH="220" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="time_out" TYPE="char" LENGTH="220" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="total_work_time" TYPE="char" LENGTH="220" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="work_duration" TYPE="char" LENGTH="220" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="log_date" TYPE="char" LENGTH="220" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="timecreated" TYPE="char" LENGTH="220" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="timeupdated" TYPE="char" LENGTH="220" NOTNULL="false" SEQUENCE="false"/>
        </FIELDS>
        <KEYS>
        <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
        </KEYS>
    </TABLE>
</TABLES>
</XMLDB>