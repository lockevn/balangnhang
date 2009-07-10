using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Collections;
using System.Data;

namespace GURUCORE.GForm.CodeGenerator
{
	public class MySqlSchemaReader : SchemaReader
	{
        public MySqlSchemaReader(IDbConnection p_oDbConn)
            : base(p_oDbConn)
		{
		}

		public override ArrayList GetSchemaObjects()
		{
			TypeMapper oTypeMapper = new TypeMapper("TypeMapper_MySql5.xml");
			ArrayList arrResult = new ArrayList();
			string sSQL;
			//get all table, and its fields, of current database
            sSQL = string.Format(
@"select
v1.TABLE_NAME,  v1.TABLE_TYPE,  v2.COLUMN_NAME
from   (select * from INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA = '{0}') as v1

left join 
(
SELECT k.TABLE_SCHEMA, k.TABLE_NAME, k.column_name
FROM information_schema.table_constraints t
JOIN information_schema.key_column_usage k
USING(constraint_name,table_schema,table_name)
WHERE t.constraint_type='PRIMARY KEY'
  AND t.table_schema='{0}'
) v2
  
on v1.TABLE_SCHEMA = v2.TABLE_SCHEMA
and v1.TABLE_NAME = v2.TABLE_NAME
;"

            , this.m_oDbConn.Database);
            
			m_oDbConn.Open();
			IDbCommand oDbCmdTbl = m_oDbConn.CreateCommand();
			oDbCmdTbl.CommandType = CommandType.Text;
			oDbCmdTbl.CommandText = sSQL;
			IDataReader oDrTbl = oDbCmdTbl.ExecuteReader();
			while (oDrTbl.Read())
			{
				string sName = oDrTbl["TABLE_NAME"].ToString();
				string sPrimaryKey = (oDrTbl["COLUMN_NAME"] == DBNull.Value) ? string.Empty : oDrTbl["COLUMN_NAME"].ToString();
				SchemaObject oSchemaObject = new SchemaObject(oDrTbl["TABLE_TYPE"].ToString(),sName,sPrimaryKey,oTypeMapper);

                string sTablePrefixInConfig = GlobalOptions.GetInstance().CurrentProfile.TablePrefixes;
                if (string.IsNullOrEmpty(sTablePrefixInConfig) /*if Not config*/ ||
                    sName.StartsWith(sTablePrefixInConfig))
				{
					arrResult.Add(oSchemaObject);
				}
			}
			oDrTbl.Close();

			foreach (SchemaObject oSchemaObject in arrResult)
			{
				string sName = oSchemaObject.Name;
				string sPrimaryKey = oSchemaObject.PrimaryKey;
				//select all column
				sSQL = string.Format(
					@" select COLUMN_NAME, DATA_TYPE from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='{0}'", 
                    sName);					

				IDbCommand oDbCmdCol = m_oDbConn.CreateCommand();
				oDbCmdCol.CommandType = CommandType.Text;
				oDbCmdCol.CommandText = sSQL;
				IDataReader oDrCol = oDbCmdCol.ExecuteReader();
				int nColCount = 0;
				while (oDrCol.Read())
				{
					string sColumnName = oDrCol["COLUMN_NAME"].ToString();
					string sDataType = oDrCol["DATA_TYPE"].ToString();
					SchemaObjectProperty oSchemaObjectProperty = new SchemaObjectProperty(sColumnName,sDataType,sColumnName == sPrimaryKey,sColumnName == sPrimaryKey,false);
					oSchemaObject.Properties.Add(oSchemaObjectProperty);
					nColCount ++;
				}
				oDrCol.Close();
			}

			m_oDbConn.Close();

			return arrResult;
		}
	}
}
