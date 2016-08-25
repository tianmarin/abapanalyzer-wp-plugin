# sap-basis-parameters-wp-plugin
A wordpress plugin, that checks multiple 'SAP BASIS Profiles' and shows differences between them and some commendations 


SAP Basis Parameters are saved as files somewhere into the host that gives life to a SAP NW System.
This project is focused into 'SAP Netweaver ABAP Application Server' profiles:
* DEFAULT
* ~~START~~
* INSTANCE

The Start Profiles and start parameters are for now outside our main goal, which is:
> Provide a simple tool to check SAP NW ABAP AS profiles tools to identify different parameters values between profiles. 

Some of the possible scenarios we would like to solve are:
* Check different instance profiles, and compare the values of same parameters.
* Propose homogeneous parameters values.
* Group parameters by kind and scenarios.
* Execute a sappfpar-kind analysis and check bad assigned values for simple parameters.
