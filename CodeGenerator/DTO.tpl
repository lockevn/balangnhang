using System;
using System.Collections;
using System.Xml.Serialization;

using GURUCORE.Framework.Core.Data;

namespace $namespace
{
	/// <summary>
	/// Summary for $tool.Append($tool.TruncatePrefix($schemaObject.Name),"DTO").
	/// </summary>
	[Serializable]
	[System.Diagnostics.DebuggerStepThrough]
	[PersistenceClass("$schemaObject.Name",$tool.EvaluateNull($schemaObject.PrimaryKey),$tool.Iff($schemaObject.ObjectType,"BASE TABLE"))]
	 public sealed partial class $tool.Append($tool.TruncatePrefix($schemaObject.Name),"DTO") : DTOBase
	{
#foreach( $property in $schemaObject.Properties )
		public const string $tool.Append($property.Name.ToUpper(),"_FLD") = "$property.Name";
#end	

#foreach( $property in $schemaObject.Properties )
		private $schemaObject.TypeMapper.GetCSharpType($property.Type) $tool.Append($tool.Append("m_",$schemaObject.TypeMapper.GetCSharpTypePrefix($property.Type)),$property.Name);
#end	

		public $tool.Append($tool.TruncatePrefix($schemaObject.Name),"DTO")()
		{
		}
#foreach( $property in $schemaObject.Properties )
		
		[PersistenceProperty("$property.Name",$property.Identity.ToString().ToLower(),$property.ReadOnly.ToString().ToLower())]
		public $schemaObject.TypeMapper.GetCSharpType($property.Type) $property.Name
		{
			get
			{
				return $tool.Append($tool.Append("m_",$schemaObject.TypeMapper.GetCSharpTypePrefix($property.Type)),$property.Name);
			}
			set
			{
				m_htIsNull["$property.Name"] = false;
				$tool.Append($tool.Append("m_",$schemaObject.TypeMapper.GetCSharpTypePrefix($property.Type)),$property.Name) = value;
			}
		}
#end	

		public string IsNullSerializedString
		{
			get
			{
				return GetIsNullSerializedString();
			}
			set
			{
				SetIsNullSerializedString(value);
			}
		}
	}
}
